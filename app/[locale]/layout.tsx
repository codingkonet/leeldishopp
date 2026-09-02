import type { Metadata } from "next";
import { Header } from "@/components/Header";
import { Providers } from "@/components/Providers";
import { appName } from "@/lib/site";

export const metadata: Metadata = {
  title: appName,
  description: "Marketplace marocaine pour les meilleurs produits et services du Maroc.",
};

export default async function LocaleLayout({ children, params }: { children: React.ReactNode; params: Promise<{ locale: string }> }) {
  const { locale: requestedLocale } = await params;
  const locale = requestedLocale === "ar" ? "ar" : "fr";

  return (
    <Providers>
      <div className="min-h-screen bg-slate-50 text-slate-900">
        <Header locale={locale} />
        <main>{children}</main>
      </div>
    </Providers>
  );
}
