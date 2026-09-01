import { NextResponse } from "next/server";
import { getServerSession } from "next-auth";
import { z } from "zod";
import { authOptions } from "@/auth";
import { prisma } from "@/lib/prisma";

const orderSchema = z.object({
  items: z.array(z.object({ productId: z.string().cuid(), quantity: z.number().int().min(1).max(20) })).min(1),
  customerName: z.string().trim().min(2).max(100),
  customerPhone: z.string().trim().min(8).max(30),
  customerEmail: z.string().email().optional(),
  deliveryAddress: z.string().trim().min(8).max(300),
  city: z.string().trim().min(2).max(80),
  region: z.string().trim().max(80).optional(),
  postalCode: z.string().trim().max(20).optional(),
  deliveryNotes: z.string().trim().max(500).optional(),
  shippingMethod: z.enum(["STANDARD", "EXPRESS", "PICKUP"]).default("STANDARD"),
});

export async function POST(request: Request) {
  const parsed = orderSchema.safeParse(await request.json());
  if (!parsed.success) return NextResponse.json({ error: "Invalid order data" }, { status: 400 });

  const session = await getServerSession(authOptions);
  const productIds = parsed.data.items.map((item) => item.productId);
  const products = await prisma.product.findMany({ where: { id: { in: productIds }, isPublished: true } });

  if (products.length !== productIds.length) {
    return NextResponse.json({ error: "One or more products are unavailable" }, { status: 409 });
  }

  const items = parsed.data.items.map((item) => {
    const product = products.find((candidate) => candidate.id === item.productId)!;
    if (product.stock < item.quantity) throw new Error(`Insufficient stock for ${product.id}`);
    return { product, quantity: item.quantity };
  });

  const subtotal = items.reduce((sum, item) => sum + Number(item.product.price) * item.quantity, 0);
  const settings = await prisma.settings.findFirst({ select: { deliveryFee: true, freeShippingThreshold: true } });
  const shippingCost = subtotal >= Number(settings?.freeShippingThreshold ?? 500) ? 0 : Number(settings?.deliveryFee ?? 25);
  const orderNumber = `LBS-${Date.now().toString().slice(-8)}-${Math.floor(Math.random() * 90 + 10)}`;

  try {
    const order = await prisma.$transaction(async (transaction) => {
      for (const item of items) {
        const changed = await transaction.product.updateMany({
          where: { id: item.product.id, stock: { gte: item.quantity } },
          data: { stock: { decrement: item.quantity } },
        });
        if (changed.count !== 1) throw new Error("Stock changed before checkout");
      }

      return transaction.order.create({
        data: {
          orderNumber,
          userId: session?.user?.id,
          subtotal,
          shippingCost,
          total: subtotal + shippingCost,
          customerName: parsed.data.customerName,
          customerPhone: parsed.data.customerPhone,
          customerEmail: parsed.data.customerEmail,
          deliveryAddress: parsed.data.deliveryAddress,
          city: parsed.data.city,
          region: parsed.data.region,
          postalCode: parsed.data.postalCode,
          deliveryNotes: parsed.data.deliveryNotes,
          shippingMethod: parsed.data.shippingMethod,
          paymentMethod: "COD",
          payment: { create: { method: "COD", amount: subtotal + shippingCost } },
          items: { create: items.map((item) => ({ productId: item.product.id, quantity: item.quantity, price: item.product.price })) },
        },
        select: { id: true, orderNumber: true, total: true, status: true, paymentStatus: true },
      });
    });

    return NextResponse.json({ order }, { status: 201 });
  } catch (error) {
    return NextResponse.json({ error: error instanceof Error ? error.message : "Could not create order" }, { status: 409 });
  }
}
