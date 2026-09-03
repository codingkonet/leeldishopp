import { NextResponse } from "next/server";
import { prisma } from "@/lib/prisma";

export async function GET(request: Request) {
  const { searchParams } = new URL(request.url);
  const category = searchParams.get("category");
  const query = searchParams.get("q")?.trim();
  const page = Math.max(1, Number(searchParams.get("page") ?? 1));
  const pageSize = Math.min(48, Math.max(1, Number(searchParams.get("limit") ?? 12)));

  const where = {
    isPublished: true,
    ...(category ? { category: { slug: category } } : {}),
    ...(query
      ? {
          OR: [
            { nameFr: { contains: query, mode: "insensitive" as const } },
            { nameAr: { contains: query, mode: "insensitive" as const } },
            { brand: { contains: query, mode: "insensitive" as const } },
          ],
        }
      : {}),
  };

  const [products, total] = await prisma.$transaction([
    prisma.product.findMany({
      where,
      include: { images: { take: 1, orderBy: { sortOrder: "asc" } }, category: true },
      skip: (page - 1) * pageSize,
      take: pageSize,
      orderBy: { createdAt: "desc" },
    }),
    prisma.product.count({ where }),
  ]);

  return NextResponse.json({ products, page, pageSize, total });
}
