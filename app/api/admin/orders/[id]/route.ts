import { NextResponse } from "next/server";
import { OrderStatus } from "@prisma/client";
import { getServerSession } from "next-auth";
import { z } from "zod";
import { authOptions } from "@/auth";
import { canManageOrders } from "@/lib/authorization";
import { prisma } from "@/lib/prisma";

const statusSchema = z.object({ status: z.nativeEnum(OrderStatus) });

export async function PATCH(request: Request, { params }: { params: Promise<{ id: string }> }) {
  const session = await getServerSession(authOptions);
  if (!session?.user || !canManageOrders(session.user.role)) {
    return NextResponse.json({ error: "Forbidden" }, { status: 403 });
  }

  const parsed = statusSchema.safeParse(await request.json());
  if (!parsed.success) return NextResponse.json({ error: "Invalid status" }, { status: 400 });

  const { id } = await params;
  const order = await prisma.order.update({
    where: { id },
    data: { status: parsed.data.status },
    select: { id: true, orderNumber: true, status: true, paymentStatus: true },
  });

  return NextResponse.json({ order });
}
