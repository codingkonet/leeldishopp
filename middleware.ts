import { withAuth } from "next-auth/middleware";
import { NextResponse } from "next/server";
import type { NextRequestWithAuth } from "next-auth/middleware";

const adminRoles = new Set(["SUPER_ADMIN", "ADMIN", "MANAGER", "ORDER_MANAGER", "PRODUCT_MANAGER"]);

export default withAuth(
  function middleware(request: NextRequestWithAuth) {
    if (request.nextUrl.pathname.startsWith("/admin") && !adminRoles.has(request.nextauth.token?.role as string)) {
      return NextResponse.redirect(new URL("/fr/account/login", request.url));
    }
  },
  {
    callbacks: {
      authorized: ({ token }) => Boolean(token),
    },
  },
);

export const config = {
  matcher: ["/admin/:path*"],
};
