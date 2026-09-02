"use client";

import Link from "next/link";
import { use } from "react";
import { Trash2 } from "lucide-react";
import { useCart } from "@/lib/cart-context";
import { formatPrice } from "@/lib/site";

export default function CartPage({ params }: { params: Promise<{ locale: string }> }) {
  const { locale: requestedLocale } = use(params);
  const locale = requestedLocale === "ar" ? "ar" : "fr";
  const { items, updateQuantity, removeItem, subtotal } = useCart();
  const shippingFee = items.length === 0 || subtotal >= 500 ? 0 : 25;
  const total = subtotal + shippingFee;

  return (
    <div dir={locale === "ar" ? "rtl" : "ltr"} className="mx-auto max-w-7xl px-4 py-10">
      <h1 className="text-3xl font-black text-slate-900">{locale === "fr" ? "Panier" : "السلة"}</h1>

      {items.length === 0 ? (
        <div className="mt-10 rounded-2xl border bg-white p-10 text-center text-slate-600">
          <p>{locale === "fr" ? "Votre panier est vide." : "سلتك فارغة."}</p>
          <Link href={`/${locale}/products`} className="mt-4 inline-block rounded-full bg-[#b47d2d] px-5 py-3 text-sm font-semibold text-white">
            {locale === "fr" ? "Voir les produits" : "تصفح المنتجات"}
          </Link>
        </div>
      ) : (
      <div className="mt-8 grid gap-8 lg:grid-cols-[1.5fr_0.7fr]">
        <div className="space-y-4">
          {items.map((item) => (
            <div key={item.productId} className="flex items-center justify-between rounded-2xl border bg-white p-4 shadow-sm">
              <div>
                <h3 className="text-lg font-semibold text-slate-900">{locale === "fr" ? item.nameFr : item.nameAr}</h3>
                <p className="text-sm text-slate-500">{formatPrice(item.price, locale)} / unitaire</p>
              </div>
              <div className="flex items-center gap-4">
                <div className="flex items-center gap-2 rounded-full border px-2 py-1">
                  <button onClick={() => updateQuantity(item.productId, item.quantity - 1)} className="h-6 w-6">-</button>
                  <span className="w-6 text-center text-sm">{item.quantity}</span>
                  <button onClick={() => updateQuantity(item.productId, item.quantity + 1)} className="h-6 w-6">+</button>
                </div>
                <button onClick={() => removeItem(item.productId)} className="rounded-full bg-red-50 p-2 text-red-600"><Trash2 className="h-4 w-4" /></button>
              </div>
            </div>
          ))}
        </div>

        <aside className="rounded-[24px] border bg-white p-6 shadow-sm">
          <h2 className="text-xl font-bold text-slate-900">{locale === "fr" ? "Résumé" : "ملخص"}</h2>
          <div className="mt-6 space-y-3 text-sm text-slate-600">
            <div className="flex justify-between"><span>Sous-total</span><span>{formatPrice(subtotal, locale)}</span></div>
            <div className="flex justify-between"><span>Livraison</span><span>{formatPrice(shippingFee, locale)}</span></div>
            <div className="flex justify-between border-t pt-3 text-base font-bold text-slate-900"><span>Total</span><span>{formatPrice(total, locale)}</span></div>
          </div>
          <Link href={`/${locale}/checkout`} className="mt-6 block rounded-full bg-[#1f2937] px-5 py-3 text-center font-semibold text-white">
            {locale === "fr" ? "Passer commande" : "إتمام الطلب"}
          </Link>
          <Link href={`/${locale}/products`} className="mt-3 block rounded-full border border-slate-300 px-5 py-3 text-center font-semibold text-slate-700">
            {locale === "fr" ? "Continuer les achats" : "متابعة التسوق"}
          </Link>
        </aside>
      </div>
      )}
    </div>
  );
}
