import { allServices } from "@/lib/site";

export default async function ServicesPage({ params }: { params: Promise<{ locale: string }> }) {
  const { locale: requestedLocale } = await params;
  const locale = requestedLocale === "ar" ? "ar" : "fr";

  return (
    <div dir={locale === "ar" ? "rtl" : "ltr"} className="mx-auto max-w-7xl px-4 py-10">
      <h1 className="text-3xl font-black text-slate-900">{locale === "fr" ? "Services marocains" : "خدمات مغربية"}</h1>
      <div className="mt-8 grid gap-6 md:grid-cols-2">
        {allServices.map((service) => (
          <div key={service.id} className="rounded-[24px] border bg-white p-6 shadow-sm">
            <div className="mb-3 text-xs uppercase tracking-[0.2em] text-[#b47d2d]">Service</div>
            <h2 className="text-2xl font-bold text-slate-900">{locale === "fr" ? service.titleFr : service.titleAr}</h2>
            <p className="mt-3 text-slate-600">{locale === "fr" ? service.descriptionFr : service.descriptionAr}</p>
            <div className="mt-6 flex items-center justify-between text-sm text-slate-600">
              <span>{service.location}</span>
              <span className="rounded-full bg-[#fdf4d8] px-3 py-1 font-semibold text-[#8a5a12]">{service.price} DH</span>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
