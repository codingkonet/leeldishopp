import Link from "next/link";
import { getServerSession } from "next-auth";
import { redirect } from "next/navigation";
import { authOptions } from "@/auth";
import { prisma } from "@/lib/prisma";
import { getOrderStatusLabel } from "@/lib/demo";
import { formatPrice } from "@/lib/site";

export default async function AccountOrdersPage({ params }: { params: Promise<{ locale: string }> }) {
  const { locale: requestedLocale } = await params;
  const locale = requestedLocale === "ar" ? "ar" : "fr";
  const session = await getServerSession(authOptions);
  if (!session?.user) redirect(`/${locale}/account/login`);

  const orders = await prisma.order.findMany({
    where: { userId: session.user.id },
    orderBy: { createdAt: "desc" },
    select: { id: true, orderNumber: true, total: true, status: true, createdAt: true },
  });

  return (
    <div dir={locale === "ar" ? "rtl" : "ltr"} className="mx-auto max-w-5xl px-4 py-10">
      <Link href={`/${locale}/account`} className="text-sm font-semibold text-[#b47d2d]">
        {locale === "fr" ? "← Retour au compte" : "← العودة للحساب"}
      </Link>
      <h1 className="mt-3 text-3xl font-black text-slate-900">{locale === "fr" ? "Mes commandes" : "طلباتي"}</h1>

      {orders.length === 0 ? (
        <p className="mt-8 rounded-2xl border bg-white p-8 text-center text-slate-600">
          {locale === "fr" ? "Vous n’avez pas encore de commande." : "ليس لديك أي طلب بعد."}
        </p>
      ) : (
        <div className="mt-8 overflow-hidden rounded-2xl border bg-white shadow-sm">
          <table className="w-full text-left text-sm">
            <thead className="bg-slate-50 text-slate-500">
              <tr>
                <th className="px-6 py-3 font-medium">{locale === "fr" ? "Commande" : "الطلب"}</th>
                <th className="px-6 py-3 font-medium">{locale === "fr" ? "Date" : "التاريخ"}</th>
                <th className="px-6 py-3 font-medium">{locale === "fr" ? "Statut" : "الحالة"}</th>
                <th className="px-6 py-3 font-medium">{locale === "fr" ? "Total" : "الإجمالي"}</th>
              </tr>
            </thead>
            <tbody>
              {orders.map((order) => (
                <tr key={order.id} className="border-t">
                  <td className="px-6 py-4 font-semibold text-slate-900">{order.orderNumber}</td>
                  <td className="px-6 py-4 text-slate-600">
                    {new Intl.DateTimeFormat(locale === "fr" ? "fr-FR" : "ar-MA").format(order.createdAt)}
                  </td>
                  <td className="px-6 py-4">
                    <span className="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-800">
                      {getOrderStatusLabel(order.status, locale)}
                    </span>
                  </td>
                  <td className="px-6 py-4 font-semibold">{formatPrice(order.total.toNumber(), locale)}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}
    </div>
  );
}
