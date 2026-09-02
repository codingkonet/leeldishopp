"use client";

import { useRouter } from "next/navigation";
import { useState } from "react";
import { useCart } from "@/lib/cart-context";
import type { Product } from "@/lib/demo";

export function ProductActions({ product, locale }: { product: Product; locale: "fr" | "ar" }) {
  const { addItem } = useCart();
  const router = useRouter();
  const [added, setAdded] = useState(false);

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

  function handleBuyNow() {
    addItem({
      productId: product.id,
      slug: product.slug,
      nameFr: product.nameFr,
      nameAr: product.nameAr,
      price: product.price,
      image: product.image,
    });
    router.push(`/${locale}/checkout`);
  }

  return (
    <div className="grid gap-3 sm:grid-cols-2">
      <button onClick={handleAddToCart} className="rounded-full bg-[#1f2937] px-5 py-3 font-semibold text-white">
        {added ? (locale === "fr" ? "Ajouté ✓" : "أُضيف ✓") : locale === "fr" ? "Ajouter au panier" : "أضف إلى السلة"}
      </button>
      <button onClick={handleBuyNow} className="rounded-full border border-[#1f2937] px-5 py-3 font-semibold text-[#1f2937]">
        {locale === "fr" ? "Acheter maintenant" : "تسوق الآن"}
      </button>
    </div>
  );
}
