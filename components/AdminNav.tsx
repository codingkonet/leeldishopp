import Link from "next/link";
import { BarChart3, Boxes, LayoutDashboard, ListOrdered, Settings, Users } from "lucide-react";

const links = [
  { href: "/admin", label: "Vue d’ensemble", icon: LayoutDashboard },
  { href: "/admin/orders", label: "Commandes", icon: ListOrdered },
  { href: "/admin/products", label: "Produits", icon: Boxes },
  { href: "/admin/customers", label: "Clients", icon: Users },
  { href: "/admin/settings", label: "Paramètres", icon: Settings },
];

export function AdminNav() {
  return (
    <aside className="border-r bg-[#172033] text-slate-100">
      <Link href="/admin" className="block border-b border-white/10 px-6 py-5 text-xl font-black">
        Lebeldi<span className="text-[#d9a84b]">Shop</span>
        <span className="ml-2 text-xs font-medium text-slate-400">ADMIN</span>
      </Link>
      <nav className="space-y-1 p-3">
        {links.map(({ href, label, icon: Icon }) => (
          <Link key={href} href={href} className="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium text-slate-300 hover:bg-white/10 hover:text-white">
            <Icon className="h-4 w-4" /> {label}
          </Link>
        ))}
      </nav>
      <div className="mt-auto border-t border-white/10 p-4 text-xs text-slate-400">
        <BarChart3 className="mr-2 inline h-4 w-4" /> Données en temps réel
      </div>
    </aside>
  );
}
