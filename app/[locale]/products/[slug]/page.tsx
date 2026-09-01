import Image from "next/image";
import Link from "next/link";
import { notFound } from "next/navigation";
import { ProductCard } from "@/components/ProductCard";
import { allProducts, formatPrice } from "@/lib/demo";

export function generateStaticParams() {
  return allProducts.map((product) => ({ locale: "fr", slug: product.slug }));
}

export default function ProductDetail({ params }: { params: { locale: string; slug: string } }) {
  const locale = params.locale === "ar" ? "ar" : "fr";
  const product = allProducts.find((item) => item.slug === params.slug);

  if (!product) {
    notFound();
  }

  const related = allProducts.filter((item) => item.id !== product.id).slice(0, 3);

  return (
    <div dir={locale === "ar" ? "rtl" : "ltr"} className="mx-auto max-w-7xl px-4 py-10">
      <div className="mb-6 text-sm text-slate-500">
        <Link href={`/${locale}`} className="hover:text-[#b47d2d]">Accueil</Link>
        <span className="mx-2">/</span>
        <Link href={`/${locale}/products`} className="hover:text-[#b47d2d]">Produits</Link>
      </div>

      <div className="grid gap-8 lg:grid-cols-[1.2fr_0.8fr]">
        <div className="space-y-4">
          <div className="relative h-[500px] overflow-hidden rounded-[24px] bg-slate-100">
            <Image src={product.image} alt={locale === "fr" ? product.nameFr : product.nameAr} fill className="object-cover" sizes="(max-width: 1024px) 100vw, 60vw" />
          </div>
          <div className="grid grid-cols-3 gap-3">
            {[product.image, product.image, product.image].map((image, index) => (
              <div key={index} className="relative h-28 overflow-hidden rounded-2xl bg-slate-200">
                <Image src={image} alt={product.nameFr} fill className="object-cover" sizes="(max-width: 768px) 33vw, 20vw" />
              </div>
            ))}
          </div>
        </div>

        <div className="space-y-6 rounded-[24px] border bg-white p-6 shadow-sm">
          <div>
            <div className="text-xs uppercase tracking-[0.2em] text-[#b47d2d]">{product.category}</div>
            <h1 className="mt-2 text-3xl font-black text-slate-900">{locale === "fr" ? product.nameFr : product.nameAr}</h1>
          </div>

          <div className="flex items-center gap-3">
            <span className="text-3xl font-black text-[#1f2937]">{formatPrice(product.price, locale)}</span>
            {product.oldPrice ? <span className="text-lg text-slate-400 line-through">{formatPrice(product.oldPrice, locale)}</span> : null}
          </div>

          <p className="text-slate-600">{locale === "fr" ? product.descriptionFr : product.descriptionAr}</p>

          <div className="grid gap-3 sm:grid-cols-2">
            <button className="rounded-full bg-[#1f2937] px-5 py-3 font-semibold text-white">Ajouter au panier</button>
            <button className="rounded-full border border-[#1f2937] px-5 py-3 font-semibold text-[#1f2937]">Acheter maintenant</button>
          </div>

          <div className="rounded-2xl bg-slate-50 p-4 text-sm text-slate-700">
            <div className="flex items-center justify-between border-b py-2"><span>Disponibilité</span><span className="font-semibold text-emerald-700">En stock</span></div>
            <div className="flex items-center justify-between border-b py-2"><span>Livraison</span><span>24h - 72h</span></div>
            <div className="flex items-center justify-between py-2"><span>Vendeur</span><span>LebeldiShop</span></div>
          </div>
        </div>
      </div>

      <section className="mt-14">
        <h2 className="mb-6 text-2xl font-black text-slate-900">Produits similaires</h2>
        <div className="grid gap-6 md:grid-cols-3">
          {related.map((item) => (
            <ProductCard key={item.id} product={item} locale={locale} />
          ))}
        </div>
      </section>
    </div>
  );
}
