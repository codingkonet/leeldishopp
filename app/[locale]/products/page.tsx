import Link from "next/link";
import { ProductCard } from "@/components/ProductCard";
import { allProducts, dictionary } from "@/lib/site";

export function generateStaticParams() {
  return [{ locale: "fr" }, { locale: "ar" }];
}

export default async function ProductCatalog({ params }: { params: Promise<{ locale: string }> }) {
  const { locale: requestedLocale } = await params;
  const locale = requestedLocale === "ar" ? "ar" : "fr";
  const t = dictionary[locale];

  return (
    <div dir={locale === "ar" ? "rtl" : "ltr"} className="mx-auto max-w-7xl px-4 py-10">
      <div className="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
          <p className="text-sm font-semibold uppercase tracking-[0.2em] text-[#b47d2d]">{t.shop}</p>
          <h1 className="mt-2 text-3xl font-black text-slate-900">{locale === "fr" ? "Boutique marocaine" : "المتجر المغربي"}</h1>
        </div>
        <div className="flex flex-wrap gap-2 text-sm">
          <button className="rounded-full bg-[#1f2937] px-4 py-2 font-medium text-white">Tous</button>
          <button className="rounded-full border bg-white px-4 py-2 font-medium text-slate-700">Mode</button>
          <button className="rounded-full border bg-white px-4 py-2 font-medium text-slate-700">Artisanat</button>
        </div>
      </div>

      <div className="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
        {allProducts.map((product) => (
          <ProductCard key={product.id} product={product} locale={locale} />
        ))}
      </div>

      <div className="mt-10 flex justify-center">
        <Link href={`/${locale}`} className="rounded-full bg-[#b47d2d] px-5 py-3 text-sm font-semibold text-white">
          {t.continueShopping}
        </Link>
      </div>
    </div>
  );
}
