This is a [Next.js](https://nextjs.org) project bootstrapped with [`create-next-app`](https://nextjs.org/docs/app/api-reference/cli/create-next-app).

## Getting Started

First, run the development server:

```bash
npm run dev
# or
yarn dev
# or
pnpm dev
# or
bun dev
```

Open [http://localhost:3000](http://localhost:3000) with your browser to see the result.

You can start editing the page by modifying `app/page.tsx`. The page auto-updates as you edit the file.

This project uses [`next/font`](https://nextjs.org/docs/app/building-your-application/optimizing/fonts) to automatically optimize and load [Geist](https://vercel.com/font), a new font family for Vercel.

## Learn More

To learn more about Next.js, take a look at the following resources:


You can check out [the Next.js GitHub repository](https://github.com/vercel/next.js) - your feedback and contributions are welcome!

## PHP/MySQL product imports

The PHP app includes `/admin/import-products.php` for CSV exports from AliExpress, Alibaba, and
Moroccan suppliers. Download the CSV template, map the supplier columns, select the source, and
upload it. Existing products are updated by SKU, new categories are created automatically, and
imported products stay unpublished unless you choose to publish them. Source name and source URL
are retained for review.

For databases installed before product imports were added, run:

```sql
ALTER TABLE products ADD COLUMN source_name VARCHAR(120) NULL AFTER category_id,
ADD COLUMN source_url VARCHAR(500) NULL AFTER source_name;
```

Use official APIs or authorized catalog exports. Do not bypass anti-bot controls or copy images,
trademarks, descriptions, or prices without permission.

## Deploy on Vercel

The easiest way to deploy your Next.js app is to use the [Vercel Platform](https://vercel.com/new?utm_medium=default-template&filter=next.js&utm_source=create-next-app&utm_campaign=create-next-app-readme) from the creators of Next.js.

Check out our [Next.js deployment documentation](https://nextjs.org/docs/app/building-your-application/deploying) for more details.

## PHP/MySQL settings and themes

The PHP version includes `/setup.php` for installation. After installation, administrators can
open `/admin/settings.php` to edit the shop title, French and Arabic slogans, logo, favicon,
colors, currency, shipping options, SEO fields, maintenance mode, and active theme.

Logo and favicon can now be uploaded directly from the settings page as PNG, JPG, WEBP, or ICO.
Uploaded files are stored in `assets/uploads/`; the server blocks PHP execution in that folder.

Built-in themes: Atlas, Sahara, and Zellige. For an existing database, run
`database/migrations/002_store_settings.sql` before opening the settings page.
