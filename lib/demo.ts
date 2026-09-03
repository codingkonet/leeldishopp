export type Language = "fr" | "ar";

export type Category = {
  id: string;
  slug: string;
  nameFr: string;
  nameAr: string;
  descriptionFr: string;
  descriptionAr: string;
  image: string;
};

export type Product = {
  id: string;
  slug: string;
  nameFr: string;
  nameAr: string;
  descriptionFr: string;
  descriptionAr: string;
  price: number;
  oldPrice?: number;
  discount?: number;
  stock: number;
  category: string;
  image: string;
  popular?: boolean;
  newItem?: boolean;
  featured?: boolean;
  rating?: number;
};

export type Service = {
  id: string;
  titleFr: string;
  titleAr: string;
  descriptionFr: string;
  descriptionAr: string;
  price: number;
  location: string;
  image: string;
};

export const categories: Category[] = [
  { id: "mode", slug: "mode-marocaine", nameFr: "Mode marocaine", nameAr: "الموضة المغربية", descriptionFr: "Élégance contemporaine inspirée du Maroc", descriptionAr: "أناقة معاصرة مستوحاة من المغرب", image: "https://images.unsplash.com/photo-1529139574466-a303027c1d8b?auto=format&fit=crop&w=900&q=80" },
  { id: "vetements", slug: "vetements", nameFr: "Vêtements", nameAr: "ملابس", descriptionFr: "Tenues élégantes et confortables", descriptionAr: "ملابس أنيقة ومريحة", image: "https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=900&q=80" },
  { id: "artisanat", slug: "artisanat-marocain", nameFr: "Artisanat marocain", nameAr: "الحرف المغربية", descriptionFr: "Produits faits main et authentiques", descriptionAr: "منتجات يدوية أصلية", image: "https://images.unsplash.com/photo-1512436991641-6745cdb1723f?auto=format&fit=crop&w=900&q=80" },
  { id: "cosmetique", slug: "cosmetiques", nameFr: "Cosmétiques", nameAr: "مستحضرات تجميل", descriptionFr: "Nourriture, beauté et bien-être", descriptionAr: "الجمال والعناية والصحة", image: "https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=900&q=80" },
  { id: "services", slug: "services", nameFr: "Services", nameAr: "خدمات", descriptionFr: "Prestations locales et artisanales", descriptionAr: "خدمات محلية وحرفية", image: "https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=900&q=80" },
];

export const products: Product[] = [
  { id: "prod-1", slug: "djellaba-traditionnelle-bleu", nameFr: "Djellaba traditionnelle bleu", nameAr: "جلابة تقليدية زرقاء", descriptionFr: "Djellaba douce et élégante pour les moments raffinés", descriptionAr: "جلابة ناعمة وأنيقة للمناسبات الراقية", price: 420, oldPrice: 520, discount: 19, stock: 14, category: "Mode marocaine", image: "https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=900&q=80", popular: true, newItem: true, featured: true, rating: 4.8 },
  { id: "prod-2", slug: "lampe-zellige-artisanale", nameFr: "Lampe zellige artisanale", nameAr: "مصباح زليج يدوي", descriptionFr: "Ambiance chaleureuse inspirée de la décoration marocaine", descriptionAr: "جو دافئ مستوحى من ديكور المغرب", price: 310, oldPrice: 390, discount: 21, stock: 20, category: "Artisanat marocain", image: "https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=900&q=80", popular: true, featured: true, rating: 4.9 },
  { id: "prod-3", slug: "huile-dargan-pure", nameFr: "Huile d’argan pure", nameAr: "زيت الأركان النقي", descriptionFr: "Huile de beauté naturelle, riche en bienfaits", descriptionAr: "زيت طبيعي غني بفوائده للبشرة والشعر", price: 180, oldPrice: 230, discount: 22, stock: 32, category: "Cosmétiques", image: "https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=900&q=80", newItem: true, rating: 4.7 },
  { id: "prod-4", slug: "sac-en-cuir-marocain", nameFr: "Sac en cuir marocain", nameAr: "حقيبة من الجلد المغربي", descriptionFr: "Sac élégant avec finition artisanale", descriptionAr: "حقيبة أنيقة بتفاصيل حرفية", price: 640, oldPrice: 780, discount: 18, stock: 9, category: "Accessoires", image: "https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=900&q=80", popular: true, rating: 4.8 },
  { id: "prod-5", slug: "tapis-beni-ourain", nameFr: "Tapis Beni Ouarain", nameAr: "سجادة بني عوران", descriptionFr: "Tapis traditionnel à motifs épurés", descriptionAr: "سجادة تقليدية بنقوش بسيطة", price: 890, stock: 7, category: "Décoration", image: "https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=900&q=80", featured: true, rating: 4.9 },
  { id: "prod-6", slug: "service-decoration-interieur", nameFr: "Service de décoration intérieure", nameAr: "خدمة الديكور الداخلي", descriptionFr: "Aménagement sur mesure pour votre lieu", descriptionAr: "تجهيز مخصص لمساحتك", price: 1800, stock: 3, category: "Services", image: "https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=900&q=80", popular: true, rating: 4.8 },
];

export const services: Service[] = [
  { id: "svc-1", titleFr: "Décoration intérieure", titleAr: "ديكور داخلي", descriptionFr: "Concevoir un intérieur marocain lumineux et moderne.", descriptionAr: "تصميم داخلي مغربي أنيق وحديث.", price: 1800, location: "Casablanca", image: "https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=900&q=80" },
  { id: "svc-2", titleFr: "Réparation & maintenance", titleAr: "إصلاح وصيانة", descriptionFr: "Services rapides pour ménages et boutiques.", descriptionAr: "خدمات سريعة للمنازل والمتاجر.", price: 450, location: "Rabat", image: "https://images.unsplash.com/photo-1581578731548-c64695cc6952?auto=format&fit=crop&w=900&q=80" },
];

export const stats = [
  { label: { fr: "Chiffre d’affaires", ar: "إيرادات" }, value: "245.8K DH" },
  { label: { fr: "Commandes", ar: "طلبات" }, value: "1,245" },
  { label: { fr: "Produits", ar: "منتجات" }, value: "3,456" },
  { label: { fr: "Clients", ar: "عملاء" }, value: "12.7K" },
];

export function formatCurrency(value: number) {
  return `${value.toLocaleString("fr-FR")} DH`;
}

export function calculateCartSummary(items: { quantity: number; price: number }[]) {
  const subtotal = items.reduce((sum, item) => sum + item.quantity * item.price, 0);
  const shippingFee = subtotal > 500 ? 0 : 25;
  const total = subtotal + shippingFee;
  return { subtotal, shippingFee, total };
}

export function getOrderStatusLabel(status: string, language: Language) {
  const labels: Record<string, Record<Language, string>> = {
    NEW: { fr: "Nouvelle", ar: "جديدة" },
    CONFIRMED: { fr: "Confirmée", ar: "مؤكدة" },
    PREPARING: { fr: "En préparation", ar: "قيد التجهيز" },
    SHIPPED: { fr: "Expédiée", ar: "تم الشحن" },
    IN_DELIVERY: { fr: "En livraison", ar: "قيد التوصيل" },
    DELIVERED: { fr: "Livrée", ar: "تم التوصيل" },
    CANCELLED: { fr: "Annulée", ar: "ملغاة" },
  };

  return labels[status]?.[language] ?? status;
}
