import Link from "next/link";
import { Trash2 } from "lucide-react";
import { calculateCartSummary, formatPrice } from "@/lib/demo";

const cartItems = [
  { id: "1", name: "Djellaba traditionnelle bleu", quantity: 1, price: 420 },
  { id: "2", name: "Huile d’argan pure", quantity: 2, price: 180 },
];

export default function CartPage({ params }: { params: { locale: string } }) {
  const locale = params.locale === "ar" ? "ar" : "fr";
  const summary = calculateCartSummary(cartItems);

  return (
    <div dir={locale === "ar" ? "rtl" : "ltr"} className="mx-auto max-w-7xl px-4 py-10">
      <h1 className="text-3xl font-black text-slate-900">{locale === "fr" ? "Panier" : "السلة"}</h1>
      <div className="mt-8 grid gap-8 lg:grid-cols-[1.5fr_0.7fr]">
        <div className="space-y-4">
          {cartItems.map((item) => (
            <div key={item.id} className="flex items-center justify-between rounded-2xl border bg-white p-4 shadow-sm">
              <div>
                <h3 className="text-lg font-semibold text-slate-900">{item.name}</h3>
                <p className="text-sm text-slate-500">{formatPrice(item.price, locale)} / unitaire</p>
              </div>
              <div className="flex items-center gap-4">
                <div className="flex items-center gap-2 rounded-full border px-2 py-1">
                  <button className="h-6 w-6">-</button>
                  <span className="w-6 text-center text-sm">{item.quantity}</span>
                  <button className="h-6 w-6">+</button>
                </div>
                <button className="rounded-full bg-red-50 p-2 text-red-600"><Trash2 className="h-4 w-4" /></button>
              </div>
            </div>
          ))}
        </div>

        <aside className="rounded-[24px] border bg-white p-6 shadow-sm">
          <h2 className="text-xl font-bold text-slate-900">{locale === "fr" ? "Résumé" : "ملخص"}</h2>
          <div className="mt-6 space-y-3 text-sm text-slate-600">
            <div className="flex justify-between"><span>Sous-total</span><span>{formatPrice(summary.subtotal, locale)}</span></div>
            <div className="flex justify-between"><span>Livraison</span><span>{formatPrice(summary.shippingFee, locale)}</span></div>
            <div className="flex justify-between border-t pt-3 text-base font-bold text-slate-900"><span>Total</span><span>{formatPrice(summary.total, locale)}</span></div>
          </div>
          <Link href={`/${locale}/checkout`} className="mt-6 block rounded-full bg-[#1f2937] px-5 py-3 text-center font-semibold text-white">
            {locale === "fr" ? "Passer commande" : "إتمام الطلب"}
          </Link>
          <Link href={`/${locale}/products`} className="mt-3 block rounded-full border border-slate-300 px-5 py-3 text-center font-semibold text-slate-700">
            {locale === "fr" ? "Continuer les achats" : "متابعة التسوق"}
          </Link>
        </aside>
      </div>
    </div>
  );
}
