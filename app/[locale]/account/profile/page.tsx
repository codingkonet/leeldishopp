import { getServerSession } from "next-auth";
import { redirect } from "next/navigation";
import { authOptions } from "@/auth";
import { SignOutButton } from "@/components/SignOutButton";

export default async function ProfilePage({ params }: { params: Promise<{ locale: string }> }) {
  const { locale: requestedLocale } = await params;
  const locale = requestedLocale === "ar" ? "ar" : "fr";
  const session = await getServerSession(authOptions);
  if (!session?.user) redirect(`/${locale}/account/login`);

  return (
    <div dir={locale === "ar" ? "rtl" : "ltr"} className="mx-auto max-w-3xl px-4 py-10">
      <h1 className="text-3xl font-black text-slate-900">{locale === "fr" ? "Profil" : "البروفايل"}</h1>
      <div className="mt-8 space-y-4 rounded-[24px] border bg-white p-6 shadow-sm">
        <div className="flex justify-between border-b pb-3">
          <span className="text-slate-500">{locale === "fr" ? "Nom" : "الاسم"}</span>
          <span className="font-semibold">{session.user.name ?? "—"}</span>
        </div>
        <div className="flex justify-between border-b pb-3">
          <span className="text-slate-500">Email</span>
          <span className="font-semibold">{session.user.email}</span>
        </div>
        <div className="flex justify-between pb-1">
          <span className="text-slate-500">{locale === "fr" ? "Rôle" : "الدور"}</span>
          <span className="font-semibold">{session.user.role}</span>
        </div>
      </div>
      <div className="mt-6">
        <SignOutButton locale={locale} />
      </div>
    </div>
  );
}
