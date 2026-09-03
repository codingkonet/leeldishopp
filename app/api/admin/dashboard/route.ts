import { NextResponse } from "next/server";
import { getServerSession } from "next-auth";
import { authOptions } from "@/auth";
import { adminRoles } from "@/lib/authorization";
import { prisma } from "@/lib/prisma";

export async function GET() {
  const session = await getServerSession(authOptions);
  if (!session?.user || !adminRoles.includes(session.user.role)) {
    return NextResponse.json({ error: "Forbidden" }, { status: 403 });
  }

  const [revenue, orders, products, customers, pendingOrders, recentOrders] = await Promise.all([
    prisma.order.aggregate({ _sum: { total: true }, where: { status: { not: "CANCELLED" } } }),
    prisma.order.count(),
    prisma.product.count(),
    prisma.customer.count(),
    prisma.order.count({ where: { status: "NEW" } }),
    prisma.order.findMany({ take: 5, orderBy: { createdAt: "desc" }, select: { orderNumber: true, customerName: true, total: true, status: true, createdAt: true } }),
  ]);

  return NextResponse.json({
    metrics: {
      revenue: revenue._sum.total ?? 0,
      orders,
      products,
      customers,
      pendingOrders,
    },
    recentOrders,
  });
}
