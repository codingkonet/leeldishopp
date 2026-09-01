"use client";

import Link from "next/link";
import { use, useState } from "react";

export default function RegisterPage({ params }: { params: Promise<{ locale: string }> }) {
  const { locale: requestedLocale } = use(params);
  const locale = requestedLocale === "ar" ? "ar" : "fr";
  const [error, setError] = useState("");
  const [isSubmitting, setIsSubmitting] = useState(false);

  async function onSubmit(formData: FormData) {
    setError("");
    setIsSubmitting(true);
    const response = await fetch("/api/auth/register", { method: "POST", headers: { "Content-Type": "application/json" }, body: JSON.stringify({ name: formData.get("name"), email: formData.get("email"), phone: formData.get("phone") || undefined, password: formData.get("password") }) });
    setIsSubmitting(false);
    if (!response.ok) {
      setError(locale === "fr" ? "Impossible de créer le compte. Vérifiez vos informations." : "تعذر إنشاء الحساب. تحقق من المعلومات.");
      return;
    }
    window.location.assign(`/${locale}/account/login`);
  }

  return (
    <main dir={locale === "ar" ? "rtl" : "ltr"} className="mx-auto flex min-h-[70vh] max-w-md items-center px-4 py-10">
      <form action={onSubmit} className="w-full rounded-[24px] border bg-white p-7 shadow-sm">
        <p className="text-sm font-semibold text-[#b47d2d]">LEBELDISHOP</p>
        <h1 className="mt-2 text-3xl font-black text-slate-900">{locale === "fr" ? "Créer un compte" : "إنشاء حساب"}</h1>
        {error && <p className="mt-4 rounded-xl bg-red-50 p-3 text-sm text-red-700">{error}</p>}
        <div className="mt-6 space-y-4">
          <input name="name" required minLength={2} className="w-full rounded-xl border px-4 py-3" placeholder={locale === "fr" ? "Nom complet" : "الاسم الكامل"} />
          <input name="email" type="email" required className="w-full rounded-xl border px-4 py-3" placeholder="Email" />
          <input name="phone" className="w-full rounded-xl border px-4 py-3" placeholder={locale === "fr" ? "Téléphone (optionnel)" : "الهاتف (اختياري)"} />
          <input name="password" type="password" required minLength={8} className="w-full rounded-xl border px-4 py-3" placeholder={locale === "fr" ? "Mot de passe (8 caractères minimum)" : "كلمة المرور (8 أحرف على الأقل)"} />
        </div>
        <button disabled={isSubmitting} className="mt-6 w-full rounded-full bg-[#1f2937] px-5 py-3 font-semibold text-white disabled:opacity-60">{isSubmitting ? "..." : locale === "fr" ? "Créer mon compte" : "إنشاء الحساب"}</button>
        <p className="mt-5 text-center text-sm text-slate-600"><Link className="font-semibold text-[#b47d2d]" href={`/${locale}/account/login`}>{locale === "fr" ? "Déjà client ? Se connecter" : "لديك حساب؟ تسجيل الدخول"}</Link></p>
      </form>
    </main>
  );
}
