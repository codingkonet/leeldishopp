import Link from "next/link";

export default function NotFound() {
  return (
    <main className="flex min-h-screen items-center justify-center bg-slate-50 px-6">
      <div className="rounded-2xl bg-white p-10 text-center shadow-sm">
        <p className="text-sm font-semibold uppercase tracking-[0.2em] text-amber-600">404</p>
        <h1 className="mt-3 text-3xl font-bold text-slate-900">Page introuvable</h1>
        <p className="mt-2 text-slate-600">La page demandée n’existe pas ou a été déplacée.</p>
        <Link href="/fr" className="mt-6 inline-flex rounded-full bg-[#1f2937] px-5 py-3 text-sm font-semibold text-white">
          Retour à l’accueil
        </Link>
      </div>
    </main>
  );
}
