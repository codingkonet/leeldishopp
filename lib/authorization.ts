import { UserRole } from "@prisma/client";

export const adminRoles: UserRole[] = [
  UserRole.SUPER_ADMIN,
  UserRole.ADMIN,
  UserRole.MANAGER,
  UserRole.ORDER_MANAGER,
  UserRole.PRODUCT_MANAGER,
];

export function canManageOrders(role?: UserRole | null) {
  return role === UserRole.SUPER_ADMIN || role === UserRole.ADMIN || role === UserRole.MANAGER || role === UserRole.ORDER_MANAGER;
}

export function canManageProducts(role?: UserRole | null) {
  return role === UserRole.SUPER_ADMIN || role === UserRole.ADMIN || role === UserRole.MANAGER || role === UserRole.PRODUCT_MANAGER;
}
