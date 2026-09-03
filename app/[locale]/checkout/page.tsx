"use client";

import Link from "next/link";
import { useRouter } from "next/navigation";
import { use, useState } from "react";
import { useCart } from "@/lib/cart-context";
import { formatPrice } from "@/lib/site";

const shippingOptions = [
  { value: "STANDARD", labelFr: "Livraison standard", labelAr: "التوصيل العادي", fee: 25 },
  { value: "EXPRESS", labelFr: "Livraison express", labelAr: "التوصيل السريع", fee: 45 },
  { value: "PICKUP", labelFr: "Retrait en point relais", labelAr: "الاستلام من نقطة التوصيل", fee: 0 },
] as const;

export default function CheckoutPage({ params }: { params: Promise<{ locale: string }> }) {
  const { locale: requestedLocale } = use(params);
  const locale = requestedLocale === "ar" ? "ar" : "fr";
  const { items, subtotal, clear } = useCart();
  const router = useRouter();
  const [shippingMethod, setShippingMethod] = useState<(typeof shippingOptions)[number]["value"]>("STANDARD");
  const [error, setError] = useState("");
  const [isSubmitting, setIsSubmitting] = useState(false);

  const selectedShipping = shippingOptions.find((option) => option.value === shippingMethod)!;
  const shippingFee = subtotal >= 500 ? 0 : selectedShipping.fee;
  const total = subtotal + shippingFee;

  async function onSubmit(formData: FormData) {
    setError("");
    if (items.length === 0) {
      setError(locale === "fr" ? "Votre panier est vide." : "سلتك فارغة.");
      return;
    }
    setIsSubmitting(true);
    try {
      const response = await fetch("/api/orders", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          items: items.map((item) => ({ productId: item.productId, quantity: item.quantity })),
          customerName: formData.get("customerName"),
          customerPhone: formData.get("customerPhone"),
          customerEmail: formData.get("customerEmail") || undefined,
          deliveryAddress: formData.get("deliveryAddress"),
          city: formData.get("city"),
          region: formData.get("region") || undefined,
          postalCode: formData.get("postalCode") || undefined,
          deliveryNotes: formData.get("deliveryNotes") || undefined,
          shippingMethod,
        }),
      });
      const data = await response.json();
      if (!response.ok) {
        setError(data.error ?? (locale === "fr" ? "Impossible de créer la commande." : "تعذر إنشاء الطلب."));
        setIsSubmitting(false);
        return;
      }
      clear();
      router.push(`/${locale}/checkout/confirmation?order=${encodeURIComponent(data.order.orderNumber)}&total=${data.order.total}`);
    } catch {
      setError(locale === "fr" ? "Erreur réseau. Réessayez." : "خطأ في الشبكة. حاول مرة أخرى.");
      setIsSubmitting(false);
    }
  }

  return (
    <div dir={locale === "ar" ? "rtl" : "ltr"} className="mx-auto max-w-6xl px-4 py-10">
      <h1 className="text-3xl font-black text-slate-900">{locale === "fr" ? "Commande" : "الطلب"}</h1>

      <form action={onSubmit} className="mt-8 grid gap-8 lg:grid-cols-[1.3fr_0.7fr]">
        <div className="space-y-6">
          <section className="rounded-[24px] border bg-white p-6 shadow-sm">
            <h2 className="text-xl font-bold text-slate-900">{locale === "fr" ? "Informations client" : "معلومات العميل"}</h2>
            <div className="mt-5 grid gap-4 md:grid-cols-2">
              <input name="customerName" required minLength={2} className="rounded-xl border px-4 py-3" placeholder={locale === "fr" ? "Nom complet" : "الاسم الكامل"} />
              <input name="customerPhone" required minLength={8} className="rounded-xl border px-4 py-3" placeholder={locale === "fr" ? "Téléphone" : "الهاتف"} />
              <input name="customerEmail" type="email" className="rounded-xl border px-4 py-3 md:col-span-2" placeholder={locale === "fr" ? "Email (optionnel)" : "البريد الإلكتروني (اختياري)"} />
              <input name="deliveryAddress" required minLength={8} className="rounded-xl border px-4 py-3 md:col-span-2" placeholder={locale === "fr" ? "Adresse" : "العنوان"} />
              <input name="city" required className="rounded-xl border px-4 py-3" placeholder={locale === "fr" ? "Ville" : "المدينة"} />
              <input name="region" className="rounded-xl border px-4 py-3" placeholder={locale === "fr" ? "Région" : "المنطقة"} />
              <input name="postalCode" className="rounded-xl border px-4 py-3" placeholder={locale === "fr" ? "Code postal" : "الرمز البريدي"} />
              <input name="deliveryNotes" className="rounded-xl border px-4 py-3 md:col-span-2" placeholder={locale === "fr" ? "Instructions de livraison (optionnel)" : "تعليمات التوصيل (اختياري)"} />
            </div>
          </section>

          <section className="rounded-[24px] border bg-white p-6 shadow-sm">
            <h2 className="text-xl font-bold text-slate-900">{locale === "fr" ? "Livraison" : "التوصيل"}</h2>
            <div className="mt-5 space-y-3">
              {shippingOptions.map((option) => (
                <label key={option.value} className="flex items-center justify-between rounded-xl border p-4">
                  <span className="flex items-center gap-3">
                    <input
                      type="radio"
                      name="shippingOption"
                      checked={shippingMethod === option.value}
                      onChange={() => setShippingMethod(option.value)}
                    />
                    {locale === "fr" ? option.labelFr : option.labelAr}
                  </span>
                  <span className="font-semibold text-slate-700">{option.fee === 0 ? (locale === "fr" ? "Gratuit" : "مجاني") : formatPrice(option.fee, locale)}</span>
                </label>
              ))}
            </div>
          </section>

          <section className="rounded-[24px] border bg-white p-6 shadow-sm">
            <h2 className="text-xl font-bold text-slate-900">{locale === "fr" ? "Paiement" : "الدفع"}</h2>
            <div className="mt-5 rounded-2xl bg-emerald-50 p-4 text-emerald-800">
              <div className="font-semibold">COD — {locale === "fr" ? "Paiement à la livraison" : "الدفع عند الاستلام"}</div>
              <p className="mt-1 text-sm">{locale === "fr" ? "Vous payez votre commande à la réception." : "تدفع ثمن طلبك عند الاستلام."}</p>
            </div>
          </section>
        </div>

        <aside className="rounded-[24px] border bg-white p-6 shadow-sm">
          <h2 className="text-xl font-bold text-slate-900">{locale === "fr" ? "Résumé commande" : "ملخص الطلب"}</h2>
          <div className="mt-5 space-y-3 text-sm text-slate-600">
            {items.length === 0 ? (
              <p>{locale === "fr" ? "Votre panier est vide." : "سلتك فارغة."}</p>
            ) : (
              items.map((item) => (
                <div key={item.productId} className="flex justify-between">
                  <span>{locale === "fr" ? item.nameFr : item.nameAr} x{item.quantity}</span>
                  <span>{formatPrice(item.price * item.quantity, locale)}</span>
                </div>
              ))
            )}
            <div className="flex justify-between"><span>Livraison</span><span>{formatPrice(shippingFee, locale)}</span></div>
            <div className="flex justify-between border-t pt-3 text-base font-bold text-slate-900"><span>Total</span><span>{formatPrice(total, locale)}</span></div>
          </div>
          {error && <p className="mt-4 rounded-xl bg-red-50 p-3 text-sm text-red-700">{error}</p>}
          <button
            type="submit"
            disabled={isSubmitting || items.length === 0}
            className="mt-6 block w-full rounded-full bg-[#b47d2d] px-5 py-3 text-center font-semibold text-white disabled:opacity-60"
          >
            {isSubmitting ? "..." : locale === "fr" ? "Confirmer la commande" : "تأكيد الطلب"}
          </button>
          <Link href={`/${locale}/cart`} className="mt-3 block text-center text-sm font-semibold text-slate-600">
            {locale === "fr" ? "Retour au panier" : "العودة للسلة"}
          </Link>
        </aside>
      </form>
    </div>
  );
}
