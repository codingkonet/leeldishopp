import { Package, ShoppingBag, Users, Wallet } from "lucide-react";
import { prisma } from "@/lib/prisma";

function formatMad(value: unknown) {
  return `${Number(value ?? 0).toLocaleString("fr-MA", { maximumFractionDigits: 0 })} DH`;
}

export default async function AdminDashboard() {
  const [revenue, orderCount, productCount, customerCount, pendingCount, recentOrders] = await Promise.all([
    prisma.order.aggregate({ _sum: { total: true }, where: { status: { not: "CANCELLED" } } }),
    prisma.order.count(),
    prisma.product.count(),
    prisma.customer.count(),
    prisma.order.count({ where: { status: "NEW" } }),
    prisma.order.findMany({ take: 6, orderBy: { createdAt: "desc" }, select: { id: true, orderNumber: true, customerName: true, total: true, status: true, createdAt: true } }),
  ]);

  const metrics = [
    { label: "Chiffre d’affaires", value: formatMad(revenue._sum.total), icon: Wallet, color: "bg-emerald-50 text-emerald-700" },
    { label: "Commandes", value: orderCount.toLocaleString("fr-MA"), icon: ShoppingBag, color: "bg-blue-50 text-blue-700" },
    { label: "Produits", value: productCount.toLocaleString("fr-MA"), icon: Package, color: "bg-amber-50 text-amber-700" },
    { label: "Clients", value: customerCount.toLocaleString("fr-MA"), icon: Users, color: "bg-rose-50 text-rose-700" },
  ];

  return (
    <div className="p-6 md:p-10">
      <div className="mb-8 flex flex-wrap items-end justify-between gap-4">
        <div>
          <p className="text-sm font-semibold text-[#b47d2d]">ADMINISTRATION</p>
          <h1 className="mt-1 text-3xl font-black text-slate-900">Tableau de bord</h1>
          <p className="mt-1 text-slate-600">Suivez les ventes et les opérations de votre boutique.</p>
        </div>
        <div className="rounded-xl bg-amber-100 px-4 py-2 text-sm font-semibold text-amber-900">{pendingCount} commande(s) à confirmer</div>
      </div>

      <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        {metrics.map(({ label, value, icon: Icon, color }) => (
          <section key={label} className="rounded-2xl border bg-white p-5 shadow-sm">
            <div className={`inline-flex rounded-xl p-3 ${color}`}><Icon className="h-5 w-5" /></div>
            <p className="mt-4 text-sm text-slate-500">{label}</p>
            <p className="mt-1 text-2xl font-black text-slate-900">{value}</p>
          </section>
        ))}
      </div>

      <section className="mt-7 overflow-hidden rounded-2xl border bg-white shadow-sm">
        <div className="flex items-center justify-between border-b px-6 py-5">
          <div><h2 className="font-bold text-slate-900">Dernières commandes</h2><p className="mt-1 text-sm text-slate-500">Commandes reçues récemment</p></div>
        </div>
        <div className="overflow-x-auto">
          <table className="w-full text-left text-sm">
            <thead className="bg-slate-50 text-slate-500"><tr><th className="px-6 py-3 font-medium">Commande</th><th className="px-6 py-3 font-medium">Client</th><th className="px-6 py-3 font-medium">Statut</th><th className="px-6 py-3 font-medium">Total</th></tr></thead>
            <tbody>
              {recentOrders.map((order) => <tr key={order.id} className="border-t"><td className="px-6 py-4 font-semibold text-slate-900">{order.orderNumber}</td><td className="px-6 py-4 text-slate-600">{order.customerName}</td><td className="px-6 py-4"><span className="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-800">{order.status}</span></td><td className="px-6 py-4 font-semibold">{formatMad(order.total)}</td></tr>)}
              {!recentOrders.length && <tr><td colSpan={4} className="px-6 py-10 text-center text-slate-500">Aucune commande pour le moment.</td></tr>}
            </tbody>
          </table>
        </div>
      </section>
    </div>
  );
}
