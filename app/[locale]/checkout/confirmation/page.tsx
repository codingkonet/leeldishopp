import Link from "next/link";

export default async function CheckoutConfirmationPage({ params }: { params: Promise<{ locale: string }> }) {
  const { locale: requestedLocale } = await params;
  const locale = requestedLocale === "ar" ? "ar" : "fr";

  return (
    <div dir={locale === "ar" ? "rtl" : "ltr"} className="mx-auto max-w-4xl px-4 py-16">
      <div className="rounded-[28px] border border-emerald-200 bg-emerald-50 p-8 text-center shadow-sm">
        <div className="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-600 text-3xl text-white">✓</div>
        <h1 className="mt-5 text-3xl font-black text-slate-900">{locale === "fr" ? "Commande confirmée" : "تم تأكيد الطلب"}</h1>
        <p className="mt-3 text-slate-700">
          {locale === "fr"
            ? "Votre commande a bien été enregistrée. Vous recevrez un email de confirmation sous peu."
            : "تم تسجيل طلبك بنجاح. ستتلقى رسالة تأكيد عبر البريد الإلكتروني قريباً."}
        </p>
        <div className="mt-6 rounded-2xl bg-white p-4 text-left text-slate-700 shadow-sm">
          <div className="flex items-center justify-between py-2"><span>Numéro de commande</span><strong>#LBS-1024</strong></div>
          <div className="flex items-center justify-between py-2"><span>Status</span><strong>Nouvelle</strong></div>
          <div className="flex items-center justify-between py-2"><span>Mode de paiement</span><strong>COD</strong></div>
        </div>
        <div className="mt-8 flex justify-center gap-4">
          <Link href={`/${locale}`} className="rounded-full bg-[#1f2937] px-5 py-3 font-semibold text-white">{locale === "fr" ? "Retour à l’accueil" : "العودة للرئيسية"}</Link>
          <Link href={`/${locale}/account`} className="rounded-full border border-slate-300 px-5 py-3 font-semibold text-slate-700">{locale === "fr" ? "Suivi de commande" : "تتبع الطلب"}</Link>
        </div>
      </div>
    </div>
  );
}
