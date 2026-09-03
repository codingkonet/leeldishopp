"use client";

import Link from "next/link";
import Image from "next/image";
import { ShoppingCart, Eye, Check } from "lucide-react";
import { useState } from "react";
import type { Product } from "@/lib/demo";
import { formatPrice } from "@/lib/site";
import { useCart } from "@/lib/cart-context";

export function ProductCard({ product, locale }: { product: Product; locale: "fr" | "ar" }) {
  const { addItem } = useCart();
  const [added, setAdded] = useState(false);
  const price = formatPrice(product.price, locale);
  const oldPrice = product.oldPrice ? formatPrice(product.oldPrice, locale) : null;

  function handleAddToCart() {
    addItem({
      productId: product.id,
      slug: product.slug,
      nameFr: product.nameFr,
      nameAr: product.nameAr,
      price: product.price,
      image: product.image,
    });
    setAdded(true);
    setTimeout(() => setAdded(false), 1500);
  }

  return (
    <article className="overflow-hidden rounded-2xl border bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
      <div className="relative h-56 overflow-hidden bg-slate-100">
        <Image src={product.image} alt={product.nameFr} fill className="object-cover" sizes="(max-width: 768px) 100vw, 33vw" />
        {product.discount ? (
          <span className="absolute left-3 top-3 rounded-full bg-[#c55a3a] px-2.5 py-1 text-xs font-semibold text-white">-{product.discount}%</span>
        ) : null}
      </div>
      <div className="space-y-3 p-4">
        <div className="text-xs uppercase tracking-[0.1em] text-slate-500">{product.category}</div>
        <h3 className="text-lg font-semibold text-slate-900">{locale === "fr" ? product.nameFr : product.nameAr}</h3>
        <div className="flex items-center gap-3">
          <span className="text-xl font-bold text-[#1f2937]">{price}</span>
          {oldPrice ? <span className="text-sm text-slate-400 line-through">{oldPrice}</span> : null}
        </div>
        <div className="flex items-center justify-between gap-2">
          <span className="rounded-full bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700">{product.stock > 0 ? "En stock" : "Rupture"}</span>
          <div className="flex gap-2">
            <button onClick={handleAddToCart} className="rounded-full bg-[#1f2937] p-2 text-white">
              {added ? <Check className="h-4 w-4" /> : <ShoppingCart className="h-4 w-4" />}
            </button>
            <Link href={`/${locale}/products/${product.slug}`} className="rounded-full border p-2 text-slate-700">
              <Eye className="h-4 w-4" />
            </Link>
          </div>
        </div>
      </div>
    </article>
  );
}
