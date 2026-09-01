import Link from "next/link";
import { ShoppingCart, Search, User } from "lucide-react";
import { dictionary, appName, slogan } from "@/lib/site";

export function Header({ locale }: { locale: "fr" | "ar" }) {
  const isRtl = locale === "ar";
  const t = dictionary[locale];

  return (
    <header className={`border-b bg-white/95 backdrop-blur ${isRtl ? "rtl" : "ltr"}`} dir={isRtl ? "rtl" : "ltr"}>
      <div className="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3">
        <Link href={`/${locale}`} className="flex items-center gap-3">
          <div className="rounded-xl bg-[#b47d2d] px-3 py-2 text-lg font-bold text-white">L</div>
          <div>
            <div className="text-xl font-black tracking-tight text-[#1f2937]">{appName}</div>
            <div className="text-[10px] text-slate-500">{slogan[locale]}</div>
          </div>
        </Link>

        <div className="hidden flex-1 items-center justify-center md:flex">
          <div className="flex w-full max-w-xl items-center gap-2 rounded-full border bg-slate-50 px-4 py-2">
            <Search className="h-4 w-4 text-slate-500" />
            <input
              aria-label="Search"
              placeholder={t.search}
              className="w-full bg-transparent text-sm outline-none placeholder:text-slate-400"
            />
          </div>
        </div>

        <nav className="hidden items-center gap-6 text-sm font-medium text-slate-700 md:flex">
          <Link href={`/${locale}`}>{t.home}</Link>
          <Link href={`/${locale}/products`}>{t.shop}</Link>
          <Link href={`/${locale}/services`}>{t.services}</Link>
          <Link href={`/${locale}/account`}>{t.account}</Link>
        </nav>

        <div className="flex items-center gap-3">
          <Link href={`/${locale === "fr" ? "ar" : "fr"}`} className="rounded-full border px-3 py-1.5 text-xs font-semibold text-slate-700">
            {locale === "fr" ? "العربية" : "FR"}
          </Link>
          <Link href={`/${locale}/account`} className="rounded-full border p-2 text-slate-700">
            <User className="h-4 w-4" />
          </Link>
          <Link href={`/${locale}/cart`} className="relative rounded-full bg-[#1f2937] p-2 text-white">
            <ShoppingCart className="h-4 w-4" />
            <span className="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-[#b47d2d] text-[10px] font-bold text-white">3</span>
          </Link>
        </div>
      </div>
    </header>
  );
}
