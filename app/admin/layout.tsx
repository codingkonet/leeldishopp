import type { ReactNode } from "react";
import { getServerSession } from "next-auth";
import { redirect } from "next/navigation";
import { authOptions } from "@/auth";
import { adminRoles } from "@/lib/authorization";
import { AdminNav } from "@/components/AdminNav";

export default async function AdminLayout({ children }: { children: ReactNode }) {
  const session = await getServerSession(authOptions);
  if (!session?.user || !adminRoles.includes(session.user.role)) {
    redirect("/fr/account/login");
  }

  return (
    <div className="grid min-h-screen grid-cols-[240px_1fr] bg-slate-100">
      <AdminNav />
      <main className="min-w-0">{children}</main>
    </div>
  );
}
