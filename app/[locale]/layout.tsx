import type { Metadata } from "next";
import { Header } from "@/components/Header";
import { appName } from "@/lib/site";

export const metadata: Metadata = {
  title: appName,
  description: "Marketplace marocaine pour les meilleurs produits et services du Maroc.",
};

export default function LocaleLayout({ children, params }: { children: React.ReactNode; params: { locale: string } }) {
  const locale = params.locale === "ar" ? "ar" : "fr";

  return (
    <div className="min-h-screen bg-slate-50 text-slate-900">
      <Header locale={locale} />
      <main>{children}</main>
    </div>
  );
}
