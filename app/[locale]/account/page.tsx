import Link from "next/link";
import { getServerSession } from "next-auth";
import { redirect } from "next/navigation";
import { authOptions } from "@/auth";
import { SignOutButton } from "@/components/SignOutButton";

export default async function AccountPage({ params }: { params: Promise<{ locale: string }> }) {
  const { locale: requestedLocale } = await params;
  const locale = requestedLocale === "ar" ? "ar" : "fr";
  const session = await getServerSession(authOptions);

  if (!session?.user) {
    redirect(`/${locale}/account/login`);
  }

  return (
    <div dir={locale === "ar" ? "rtl" : "ltr"} className="mx-auto max-w-6xl px-4 py-10">
      <div className="flex flex-wrap items-center justify-between gap-4">
        <div>
          <h1 className="text-3xl font-black text-slate-900">{locale === "fr" ? "Mon compte" : "حسابي"}</h1>
          <p className="mt-1 text-slate-600">{session.user.email}</p>
        </div>
        <SignOutButton locale={locale} />
      </div>
      <div className="mt-8 grid gap-6 md:grid-cols-2">
        <div className="rounded-[24px] border bg-white p-5 shadow-sm">
          <h3 className="text-lg font-bold text-slate-900">{locale === "fr" ? "Historique des commandes" : "سجل الطلبات"}</h3>
          <p className="mt-2 text-sm text-slate-600">{locale === "fr" ? "Consultez vos achats et leurs statuts." : "راجع مشترياتك وحالاتها."}</p>
          <Link href={`/${locale}/account/orders`} className="mt-4 inline-block text-sm font-semibold text-[#b47d2d]">Voir</Link>
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
