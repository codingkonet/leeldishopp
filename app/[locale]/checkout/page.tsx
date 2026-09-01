import Link from "next/link";

export default async function CheckoutPage({ params }: { params: Promise<{ locale: string }> }) {
  const { locale: requestedLocale } = await params;
  const locale = requestedLocale === "ar" ? "ar" : "fr";

  return (
    <div dir={locale === "ar" ? "rtl" : "ltr"} className="mx-auto max-w-6xl px-4 py-10">
      <h1 className="text-3xl font-black text-slate-900">{locale === "fr" ? "Commande" : "الطلب"}</h1>

      <div className="mt-8 grid gap-8 lg:grid-cols-[1.3fr_0.7fr]">
        <div className="space-y-6">
          <section className="rounded-[24px] border bg-white p-6 shadow-sm">
            <h2 className="text-xl font-bold text-slate-900">{locale === "fr" ? "Informations client" : "معلومات العميل"}</h2>
            <div className="mt-5 grid gap-4 md:grid-cols-2">
              <input className="rounded-xl border px-4 py-3" placeholder={locale === "fr" ? "Nom complet" : "الاسم الكامل"} />
              <input className="rounded-xl border px-4 py-3" placeholder={locale === "fr" ? "Téléphone" : "الهاتف"} />
              <input className="rounded-xl border px-4 py-3 md:col-span-2" placeholder={locale === "fr" ? "Email" : "البريد الإلكتروني"} />
              <input className="rounded-xl border px-4 py-3 md:col-span-2" placeholder={locale === "fr" ? "Adresse" : "العنوان"} />
              <input className="rounded-xl border px-4 py-3" placeholder={locale === "fr" ? "Ville" : "المدينة"} />
              <input className="rounded-xl border px-4 py-3" placeholder={locale === "fr" ? "Région" : "المنطقة"} />
              <input className="rounded-xl border px-4 py-3" placeholder={locale === "fr" ? "Code postal" : "الرمز البريدي"} />
            </div>
          </section>

          <section className="rounded-[24px] border bg-white p-6 shadow-sm">
            <h2 className="text-xl font-bold text-slate-900">{locale === "fr" ? "Livraison" : "التوصيل"}</h2>
            <div className="mt-5 space-y-3">
              {[
                { name: "Livraison standard", price: "25 DH" },
                { name: "Livraison express", price: "45 DH" },
                { name: "Retrait en point relais", price: "Gratis" },
              ].map((option) => (
                <label key={option.name} className="flex items-center justify-between rounded-xl border p-4">
                  <span>{option.name}</span>
                  <span className="font-semibold text-slate-700">{option.price}</span>
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
          <div className="mt-5 space-y-4 text-sm text-slate-600">
            <div className="flex justify-between"><span>Djellaba traditionnelle</span><span>420 DH</span></div>
            <div className="flex justify-between"><span>Huile d’argan x2</span><span>360 DH</span></div>
            <div className="flex justify-between"><span>Livraison</span><span>25 DH</span></div>
            <div className="flex justify-between border-t pt-3 text-base font-bold text-slate-900"><span>Total</span><span>805 DH</span></div>
          </div>
          <Link href={`/${locale}/checkout/confirmation`} className="mt-6 block rounded-full bg-[#b47d2d] px-5 py-3 text-center font-semibold text-white">
            {locale === "fr" ? "Confirmer la commande" : "تأكيد الطلب"}
          </Link>
        </aside>
      </div>
    </div>
  );
}
