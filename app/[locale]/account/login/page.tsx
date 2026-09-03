"use client";

import Link from "next/link";
import { signIn } from "next-auth/react";
import { useRouter } from "next/navigation";
import { use, useState } from "react";

export default function LoginPage({ params }: { params: Promise<{ locale: string }> }) {
  const { locale: requestedLocale } = use(params);
  const locale = requestedLocale === "ar" ? "ar" : "fr";
  const router = useRouter();
  const [error, setError] = useState("");
  const [isSubmitting, setIsSubmitting] = useState(false);

  async function onSubmit(formData: FormData) {
    setError("");
    setIsSubmitting(true);
    const result = await signIn("credentials", {
      email: String(formData.get("email")),
      password: String(formData.get("password")),
      redirect: false,
    });
    setIsSubmitting(false);
    if (result?.error) {
      setError(locale === "fr" ? "Email ou mot de passe incorrect." : "البريد الإلكتروني أو كلمة المرور غير صحيحين.");
      return;
    }
    router.push(`/${locale}/account`);
    router.refresh();
  }

  return (
    <main dir={locale === "ar" ? "rtl" : "ltr"} className="mx-auto flex min-h-[70vh] max-w-md items-center px-4 py-10">
      <form action={onSubmit} className="w-full rounded-[24px] border bg-white p-7 shadow-sm">
        <p className="text-sm font-semibold text-[#b47d2d]">LEBELDISHOP</p>
        <h1 className="mt-2 text-3xl font-black text-slate-900">{locale === "fr" ? "Connexion" : "تسجيل الدخول"}</h1>
        {error && <p className="mt-4 rounded-xl bg-red-50 p-3 text-sm text-red-700">{error}</p>}
        <div className="mt-6 space-y-4">
          <input name="email" type="email" required className="w-full rounded-xl border px-4 py-3" placeholder="Email" />
          <input name="password" type="password" required minLength={8} className="w-full rounded-xl border px-4 py-3" placeholder={locale === "fr" ? "Mot de passe" : "كلمة المرور"} />
        </div>
        <button disabled={isSubmitting} className="mt-6 w-full rounded-full bg-[#1f2937] px-5 py-3 font-semibold text-white disabled:opacity-60">{isSubmitting ? "..." : locale === "fr" ? "Se connecter" : "دخول"}</button>
        <p className="mt-5 text-center text-sm text-slate-600">{locale === "fr" ? "Nouveau client ?" : "عميل جديد؟"} <Link className="font-semibold text-[#b47d2d]" href={`/${locale}/account/register`}>{locale === "fr" ? "Créer un compte" : "إنشاء حساب"}</Link></p>
      </form>
    </main>
  );
}
