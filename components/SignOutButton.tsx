"use client";

import { signOut } from "next-auth/react";

export function SignOutButton({ locale }: { locale: "fr" | "ar" }) {
  return (
    <button
      onClick={() => signOut({ callbackUrl: `/${locale}` })}
      className="rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700"
    >
      {locale === "fr" ? "Se déconnecter" : "تسجيل الخروج"}
    </button>
  );
}
