import Link from "next/link";
import { ArrowRight, BadgePercent, Star } from "lucide-react";
import { ProductCard } from "@/components/ProductCard";
import { allCategories, allProducts, allServices, dictionary, formatPrice } from "@/lib/site";

export function generateStaticParams() {
  return [{ locale: "fr" }, { locale: "ar" }];
}

export default async function LocaleHome({ params }: { params: Promise<{ locale: string }> }) {
  const { locale: requestedLocale } = await params;
  const locale = requestedLocale === "ar" ? "ar" : "fr";
  const t = dictionary[locale];
  const featured = allProducts.slice(0, 4);
  const categories = allCategories.slice(0, 4);
  const services = allServices.slice(0, 2);

  return (
    <div dir={locale === "ar" ? "rtl" : "ltr"} className="pb-16">
      <section className="mx-auto max-w-7xl px-4 py-8 md:py-12">
        <div className="overflow-hidden rounded-[28px] bg-gradient-to-r from-[#1f2937] via-[#2d3a49] to-[#b47d2d] text-white shadow-xl">
          <div className="grid gap-8 px-6 py-10 md:grid-cols-2 md:px-10 lg:px-14">
            <div className="space-y-6">
              <span className="inline-block rounded-full bg-white/10 px-4 py-2 text-sm font-medium backdrop-blur">
                {locale === "fr" ? "Découverte du Maroc" : "اكتشف المغرب"}
              </span>
              <h1 className="text-4xl font-black tracking-tight md:text-6xl">
                {locale === "fr" ? "Découvrez le meilleur du Maroc" : "اكتشف أفضل المنتجات والخدمات المغربية"}
              </h1>
              <p className="max-w-lg text-base text-slate-200 md:text-lg">
                {locale === "fr"
                  ? "Des produits authentiques, des services de confiance et des créations marocaines à la pointe du style."
                  : "منتجات أصلية وخدمات موثوقة وإبداعات مغربية تجمع بين الأصالة والحداثة."}
              </p>
              <div className="flex gap-4">
                <Link href={`/${locale}/products`} className="inline-flex items-center gap-2 rounded-full bg-[#b47d2d] px-6 py-3 font-semibold text-white">
                  {t.buyNow} <ArrowRight className="h-4 w-4" />
                </Link>
                <Link href={`/${locale}/services`} className="inline-flex items-center gap-2 rounded-full border border-white/30 bg-white/5 px-6 py-3 font-semibold text-white">
                  {t.services}
                </Link>
              </div>
            </div>
            <div className="flex items-center justify-center">
              <div className="grid w-full max-w-md gap-4 rounded-[24px] bg-white/10 p-4 backdrop-blur-sm">
                <div className="rounded-2xl bg-white/10 p-4">
                  <div className="text-sm text-slate-200">{locale === "fr" ? "Livraison gratuite" : "توصيل مجاني"}</div>
                  <div className="mt-2 text-3xl font-bold">{locale === "fr" ? "à partir de 500 DH" : "بدءًا من 500 درهم"}</div>
                </div>
                <div className="grid grid-cols-2 gap-4">
                  {featured.slice(0, 2).map((item) => (
                    <div key={item.id} className="rounded-2xl bg-white/10 p-3">
                      <div className="mb-2 text-xs text-slate-200">{locale === "fr" ? item.nameFr : item.nameAr}</div>
                      <div className="font-bold">{formatPrice(item.price, locale)}</div>
                    </div>
                  ))}
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section className="mx-auto max-w-7xl px-4 py-8">
        <div className="mb-6 flex items-center justify-between">
          <h2 className="text-2xl font-bold text-slate-900">{t.popularProducts}</h2>
          <Link href={`/${locale}/products`} className="text-sm font-semibold text-[#b47d2d]">{t.viewDetails}</Link>
        </div>
        <div className="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
          {featured.map((product) => (
            <ProductCard key={product.id} product={product} locale={locale} />
          ))}
        </div>
      </section>

      <section className="mx-auto max-w-7xl px-4 py-8">
        <div className="mb-6 flex items-center justify-between">
          <h2 className="text-2xl font-bold text-slate-900">{t.categories}</h2>
          <span className="text-sm text-slate-500">{categories.length} catégories</span>
        </div>
        <div className="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
          {categories.map((category) => (
            <div key={category.id} className="overflow-hidden rounded-2xl border bg-white shadow-sm">
              <div className="h-40 bg-slate-100" style={{ backgroundImage: `url(${category.image})`, backgroundSize: "cover", backgroundPosition: "center" }} />
              <div className="p-4">
                <h3 className="text-lg font-semibold text-slate-900">{locale === "fr" ? category.nameFr : category.nameAr}</h3>
                <p className="mt-2 text-sm text-slate-600">{locale === "fr" ? category.descriptionFr : category.descriptionAr}</p>
              </div>
            </div>
          ))}
        </div>
      </section>

      <section className="mx-auto max-w-7xl px-4 py-8">
        <div className="mb-6 flex items-center justify-between">
          <h2 className="text-2xl font-bold text-slate-900">{t.servicesPopular}</h2>
          <Link href={`/${locale}/services`} className="text-sm font-semibold text-[#b47d2d]">{t.viewDetails}</Link>
        </div>
        <div className="grid gap-6 md:grid-cols-2">
          {services.map((service) => (
            <div key={service.id} className="rounded-2xl border bg-white p-5 shadow-sm">
              <div className="flex items-center justify-between gap-4">
                <div>
                  <div className="text-xs uppercase tracking-[0.15em] text-slate-500">Service</div>
                  <h3 className="mt-2 text-xl font-bold text-slate-900">{locale === "fr" ? service.titleFr : service.titleAr}</h3>
                </div>
                <span className="rounded-full bg-amber-100 px-3 py-1 text-sm font-semibold text-amber-800">{formatPrice(service.price, locale)}</span>
              </div>
              <p className="mt-3 text-slate-600">{locale === "fr" ? service.descriptionFr : service.descriptionAr}</p>
              <div className="mt-4 flex items-center justify-between text-sm text-slate-500">
                <span>{service.location}</span>
                <span className="inline-flex items-center gap-1 font-semibold text-[#b47d2d]">
                  <Star className="h-4 w-4 fill-current" /> 4.8
                </span>
              </div>
            </div>
          ))}
        </div>
      </section>

      <section className="mx-auto max-w-7xl px-4 py-8">
        <div className="rounded-[24px] border border-dashed border-[#b47d2d]/50 bg-[#fffaf0] p-8 text-center">
          <div className="mb-3 inline-flex items-center gap-2 rounded-full bg-[#f6e5c4] px-3 py-1 text-sm font-semibold text-[#8a5a12]">
            <BadgePercent className="h-4 w-4" />
            {t.promotions}
          </div>
          <h3 className="text-3xl font-black text-slate-900">{locale === "fr" ? "Offres exceptionnelles sur la mode marocaine" : "عروض استثنائية على الأزياء المغربية"}</h3>
          <p className="mt-3 text-slate-600">{locale === "fr" ? "Profitez de réductions sur des créations uniques et artisanales." : "استفد من خصومات على إبداعات فريدة وحرفية."}</p>
        </div>
      </section>
    </div>
  );
}
