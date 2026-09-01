import { PrismaClient } from "@prisma/client";
import bcrypt from "bcryptjs";

const prisma = new PrismaClient();

async function main() {
  const adminEmail = process.env.ADMIN_EMAIL ?? "shop@lebeldishop.com";
  const adminPassword = process.env.ADMIN_PASSWORD;

  if (!adminPassword) {
    throw new Error("ADMIN_PASSWORD environment variable is required for the initial demo super admin account.");
  }

  const adminPasswordHash = await bcrypt.hash(adminPassword, 10);

  const admin = await prisma.user.upsert({
    where: { email: adminEmail },
    update: {},
    create: {
      email: adminEmail,
      passwordHash: adminPasswordHash,
      role: "SUPER_ADMIN",
      name: "LebeldiShop Admin",
      isActive: true,
      admin: {
        create: {
          isSuper: true,
        },
      },
    },
  });

  await prisma.settings.upsert({
    where: { id: "default-settings" },
    update: {},
    create: {
      id: "default-settings",
      shopName: "LebeldiShop",
      email: "shop@lebeldishop.com",
      phone: "+212 5 00 00 00 00",
      address: "Casablanca, Maroc",
      defaultCurrency: "MAD",
      vatRate: 0.2,
      deliveryFee: 25,
      freeShippingThreshold: 500,
      localeOptions: ["fr", "ar"],
      seoTitle: "LebeldiShop | Le meilleur du Maroc, chez vous",
      seoDescription: "Marketplace marocaine pour produits et services authentiques du Maroc.",
    },
  });

  const categories = [
    { slug: "mode-marocaine", nameFr: "Mode marocaine", nameAr: "الموضة المغربية", descriptionFr: "Élégance marocaine", descriptionAr: "أناقة مغربية" },
    { slug: "vetements", nameFr: "Vêtements", nameAr: "ملابس", descriptionFr: "Style quotidien", descriptionAr: "أسلوب يومي" },
    { slug: "artisanat-marocain", nameFr: "Artisanat marocain", nameAr: "الفنون المغربية", descriptionFr: "Créations artisanales", descriptionAr: "إبداعات حرفية" },
    { slug: "cosmetiques", nameFr: "Cosmétiques", nameAr: "مستحضرات تجميل", descriptionFr: "Produits naturels", descriptionAr: "منتجات طبيعية" },
    { slug: "services", nameFr: "Services", nameAr: "خدمات", descriptionFr: "Prestations locales", descriptionAr: "خدمات محلية" },
  ];

  for (const category of categories) {
    await prisma.category.upsert({
      where: { slug: category.slug },
      update: {},
      create: {
        ...category,
        slug: category.slug,
        isActive: true,
        sortOrder: 0,
      },
    });
  }

  const modeCategory = await prisma.category.findUnique({ where: { slug: "mode-marocaine" } });
  const artisanCategory = await prisma.category.findUnique({ where: { slug: "artisanat-marocain" } });
  const servicesCategory = await prisma.category.findUnique({ where: { slug: "services" } });

  const products = [
    {
      sku: "LBS-001",
      slug: "djellaba-traditionnelle-bleu",
      nameFr: "Djellaba traditionnelle bleu", nameAr: "جلابة تقليدية زرقاء",
      descriptionFr: "Une djellaba confortable et élégante pour toutes les occasions.",
      descriptionAr: "جلابة مريحة وأنيقة مناسبة لجميع المناسبات.",
      shortDescriptionFr: "Style marocain premium",
      shortDescriptionAr: "أسلوب مغربي فاخر",
      price: 420,
      oldPrice: 520,
      stock: 14,
      brand: "Lebeldi",
      categoryId: modeCategory!.id,
      isFeatured: true,
      isPopular: true,
      isNew: true,
      isOnSale: true,
      salePercent: 19,
      rating: 4.8,
      reviewsCount: 32,
      images: ["https://images.unsplash.com/..."],
    },
    {
      sku: "LBS-002",
      slug: "lampe-zellige-artisanale",
      nameFr: "Lampe zellige artisanale", nameAr: "مصباح زليج يدوي",
      descriptionFr: "Lampe décorative inspirée du zellige marocain.",
      descriptionAr: "مصباح ديكوري مستوحى من الزليج المغربي.",
      shortDescriptionFr: "Décoration maison",
      shortDescriptionAr: "ديكور المنزل",
      price: 310,
      oldPrice: 390,
      stock: 19,
      brand: "Riad Atelier",
      categoryId: artisanCategory!.id,
      isFeatured: true,
      isPopular: true,
      isOnSale: true,
      salePercent: 21,
      rating: 4.7,
      reviewsCount: 24,
      images: ["https://images.unsplash.com/..."],
    },
  ];

  for (const product of products) {
    await prisma.product.upsert({
      where: { slug: product.slug },
      update: {},
      create: {
        ...product,
        categoryId: product.categoryId,
        dimensions: "30 x 25 x 15 cm",
        weight: 1.4,
        deliveryFee: 12,
        images: {
          create: product.images.map((url, index) => ({ url, sortOrder: index, alt: product.nameFr })),
        },
      },
    });
  }

  await prisma.service.upsert({
    where: { id: "service-demo" },
    update: {},
    create: {
      id: "service-demo",
      titleFr: "Service de décoration intérieure",
      titleAr: "خدمة الديكور الداخلي",
      descriptionFr: "Aménagement sur mesure pour maisons et boutiques.",
      descriptionAr: "تجهيزات مخصصة للمنازل والمتاجر.",
      price: 1800,
      location: "Casablanca",
      images: ["https://images.unsplash.com/..."],
      available: true,
      contactInfo: "contact@lebeldishop.com",
    },
  });

  console.log("Seed completed for LebeldiShop.");
  console.log("Admin account:", admin.email);
}

main()
  .catch((error) => {
    console.error(error);
    process.exit(1);
  })
  .finally(async () => {
    await prisma.$disconnect();
  });
