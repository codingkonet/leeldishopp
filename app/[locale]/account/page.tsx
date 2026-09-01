import Link from "next/link";

export default async function AccountPage({ params }: { params: Promise<{ locale: string }> }) {
  const { locale: requestedLocale } = await params;
  const locale = requestedLocale === "ar" ? "ar" : "fr";

  return (
    <div dir={locale === "ar" ? "rtl" : "ltr"} className="mx-auto max-w-6xl px-4 py-10">
      <h1 className="text-3xl font-black text-slate-900">{locale === "fr" ? "Mon compte" : "حسابي"}</h1>
      <div className="mt-8 grid gap-6 md:grid-cols-3">
        <div className="rounded-[24px] border bg-white p-5 shadow-sm">
          <h3 className="text-lg font-bold text-slate-900">{locale === "fr" ? "Historique des commandes" : "سجل الطلبات"}</h3>
          <p className="mt-2 text-sm text-slate-600">{locale === "fr" ? "Consultez vos achats et leurs statuts." : "راجع مشترياتك وحالاتها."}</p>
          <Link href={`/${locale}/account/orders`} className="mt-4 inline-block text-sm font-semibold text-[#b47d2d]">Voir</Link>
        </div>
        <div className="rounded-[24px] border bg-white p-5 shadow-sm">
          <h3 className="text-lg font-bold text-slate-900">{locale === "fr" ? "Favoris" : "المفضلة"}</h3>
          <p className="mt-2 text-sm text-slate-600">{locale === "fr" ? "Retrouvez vos produits préférés." : "اعثر على منتجاتك المفضلة."}</p>
          <Link href={`/${locale}/account/wishlist`} className="mt-4 inline-block text-sm font-semibold text-[#b47d2d]">Voir</Link>
        </div>
        <div className="rounded-[24px] border bg-white p-5 shadow-sm">
          <h3 className="text-lg font-bold text-slate-900">{locale === "fr" ? "Profil" : "البروفايل"}</h3>
          <p className="mt-2 text-sm text-slate-600">{locale === "fr" ? "Modifiez vos informations personnelles." : "قم بتحديث معلوماتك الشخصية."}</p>
          <Link href={`/${locale}/account/profile`} className="mt-4 inline-block text-sm font-semibold text-[#b47d2d]">Voir</Link>
        </div>
      </div>
    </div>
  );
}
