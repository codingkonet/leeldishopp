import { categories, formatCurrency, products, services, type Language } from "@/lib/demo";

export const appName = "LebeldiShop";
export const slogan = { fr: "Le meilleur du Maroc, chez vous", ar: "أفضل المنتجات المغربية، إلى منزلك" };

export const dictionary = {
  fr: {
    home: "Accueil",
    shop: "Boutique",
    services: "Services",
    categories: "Catégories",
    account: "Compte",
    cart: "Panier",
    search: "Rechercher",
    signIn: "Connexion",
    popularProducts: "Produits populaires",
    newArrivals: "Nouveautés",
    bestSellers: "Meilleures ventes",
    promotions: "Promotions",
    recommended: "Produits recommandés",
    servicesPopular: "Services populaires",
    buyNow: "Acheter maintenant",
    addToCart: "Ajouter au panier",
    viewDetails: "Voir détails",
    freeShipping: "Livraison gratuite à partir de 500 DH",
    localeLabel: "FR / العربية",
    admin: "Admin",
    orderTracking: "Suivi de commande",
    orderNumber: "Numéro de commande",
    checkout: "Commander",
    total: "Total",
    continueShopping: "Continuer les achats",
    secureCod: "Paiement à la livraison (COD)",
    codInfo: "Vous payez votre commande à la réception.",
  },
  ar: {
    home: "الرئيسية",
    shop: "المتجر",
    services: "الخدمات",
    categories: "الفئات",
    account: "الحساب",
    cart: "السلة",
    search: "بحث",
    signIn: "تسجيل الدخول",
    popularProducts: "المنتجات الشعبية",
    newArrivals: "الأحدث",
    bestSellers: "الأكثر مبيعاً",
    promotions: "العروض",
    recommended: "منتجات مقترحة",
    servicesPopular: "الخدمات الشائعة",
    buyNow: "تسوق الآن",
    addToCart: "أضف إلى السلة",
    viewDetails: "عرض التفاصيل",
    freeShipping: "توصيل مجاني عند 500 درهم",
    localeLabel: "العربية / FR",
    admin: "الإدارة",
    orderTracking: "تتبع الطلب",
    orderNumber: "رقم الطلب",
    checkout: "إتمام الطلب",
    total: "الإجمالي",
    continueShopping: "متابعة التسوق",
    secureCod: "الدفع عند الاستلام",
    codInfo: "تدفع ثمن طلبك عند الاستلام.",
  },
} as const;

export const allProducts = products;
export const allCategories = categories;
export const allServices = services;

export function getLocaleLabel(locale: Language) {
  return locale === "fr" ? "FR" : "AR";
}

export function getProductBySlug(slug: string) {
  return products.find((product) => product.slug === slug);
}

export function getFeaturedProducts() {
  return products.filter((product) => product.featured || product.popular).slice(0, 4);
}

export function getCurrencyLabel() {
  return "MAD / DH";
}

export function formatPrice(value: number, locale: Language = "fr") {
  return locale === "ar" ? `${value.toLocaleString("ar-MA")} د.م` : `${formatCurrency(value)}`;
}
