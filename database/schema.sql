-- LebeldiShop MySQL schema
-- Import with: mysql -u root -p lebeldishop < database/schema.sql

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('SUPER_ADMIN','ADMIN','MANAGER','ORDER_MANAGER','PRODUCT_MANAGER','CUSTOMER') NOT NULL DEFAULT 'CUSTOMER',
    phone VARCHAR(30) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    failed_attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
    locked_until DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(160) NOT NULL UNIQUE,
    name_fr VARCHAR(160) NOT NULL,
    name_ar VARCHAR(160) NOT NULL,
    description_fr TEXT NULL,
    description_ar TEXT NULL,
    image VARCHAR(500) NULL,
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(60) NOT NULL UNIQUE,
    slug VARCHAR(180) NOT NULL UNIQUE,
    name_fr VARCHAR(200) NOT NULL,
    name_ar VARCHAR(200) NOT NULL,
    description_fr TEXT NOT NULL,
    description_ar TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    old_price DECIMAL(10,2) NULL,
    stock INT UNSIGNED NOT NULL DEFAULT 0,
    brand VARCHAR(120) NULL,
    image VARCHAR(500) NOT NULL,
    category_id INT UNSIGNED NOT NULL,
    source_name VARCHAR(120) NULL,
    source_url VARCHAR(500) NULL,
    is_published TINYINT(1) NOT NULL DEFAULT 1,
    is_featured TINYINT(1) NOT NULL DEFAULT 0,
    is_popular TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES categories(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS services (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title_fr VARCHAR(200) NOT NULL,
    title_ar VARCHAR(200) NOT NULL,
    description_fr TEXT NOT NULL,
    description_ar TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    location VARCHAR(120) NULL,
    image VARCHAR(500) NOT NULL,
    available TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS orders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(40) NOT NULL UNIQUE,
    user_id INT UNSIGNED NULL,
    customer_name VARCHAR(150) NOT NULL,
    customer_phone VARCHAR(30) NOT NULL,
    customer_email VARCHAR(190) NULL,
    delivery_address VARCHAR(500) NOT NULL,
    city VARCHAR(120) NOT NULL,
    region VARCHAR(120) NULL,
    postal_code VARCHAR(20) NULL,
    delivery_notes VARCHAR(500) NULL,
    shipping_method ENUM('STANDARD','EXPRESS','PICKUP') NOT NULL DEFAULT 'STANDARD',
    subtotal DECIMAL(10,2) NOT NULL,
    discount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    coupon_code VARCHAR(60) NULL,
    shipping_cost DECIMAL(10,2) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    status ENUM('NEW','CONFIRMED','PREPARING','SHIPPED','IN_DELIVERY','DELIVERED','CANCELLED','RETURNED','DELIVERY_FAILED') NOT NULL DEFAULT 'NEW',
    payment_status ENUM('PENDING','PAID','FAILED','REFUNDED') NOT NULL DEFAULT 'PENDING',
    payment_method VARCHAR(20) NOT NULL DEFAULT 'COD',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS order_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    quantity INT UNSIGNED NOT NULL DEFAULT 1,
    price DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    CONSTRAINT fk_order_items_product FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS settings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    shop_name VARCHAR(120) NOT NULL DEFAULT 'LebeldiShop',
    email VARCHAR(190) NOT NULL DEFAULT 'shop@lebeldishop.com',
    phone VARCHAR(30) NULL,
    address VARCHAR(255) NULL,
    currency VARCHAR(10) NOT NULL DEFAULT 'MAD',
    delivery_fee DECIMAL(10,2) NOT NULL DEFAULT 25.00,
    free_shipping_threshold DECIMAL(10,2) NOT NULL DEFAULT 500.00,
    slogan_fr VARCHAR(255) NOT NULL DEFAULT 'Le meilleur du Maroc, chez vous',
    slogan_ar VARCHAR(255) NOT NULL DEFAULT 'أفضل المنتجات المغربية، إلى منزلك',
    primary_color VARCHAR(7) NOT NULL DEFAULT '#b47d2d',
    accent_color VARCHAR(7) NOT NULL DEFAULT '#1f2937',
    logo_url VARCHAR(500) NULL,
    favicon_url VARCHAR(500) NULL,
    theme_name VARCHAR(40) NOT NULL DEFAULT 'atlas',
    maintenance_mode TINYINT(1) NOT NULL DEFAULT 0,
    seo_title VARCHAR(255) NULL,
    seo_description VARCHAR(500) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS coupons (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(60) NOT NULL UNIQUE,
    type ENUM('PERCENTAGE','FIXED') NOT NULL,
    value DECIMAL(10,2) NOT NULL,
    minimum_order DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    max_uses INT UNSIGNED NULL,
    used_count INT UNSIGNED NOT NULL DEFAULT 0,
    active_from DATETIME NULL,
    active_until DATETIME NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS reviews (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    rating TINYINT UNSIGNED NOT NULL,
    comment TEXT NULL,
    is_approved TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_product_review (product_id, user_id),
    CONSTRAINT fk_reviews_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    CONSTRAINT fk_reviews_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS wishlists (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_wishlist_product (product_id, user_id),
    CONSTRAINT fk_wishlists_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    CONSTRAINT fk_wishlists_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS delivery_zones (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    city VARCHAR(120) NOT NULL UNIQUE,
    region VARCHAR(120) NULL,
    standard_fee DECIMAL(10,2) NOT NULL DEFAULT 25.00,
    express_fee DECIMAL(10,2) NOT NULL DEFAULT 45.00,
    is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;

-- Seed data
INSERT INTO settings (shop_name, email, phone, address, currency, delivery_fee, free_shipping_threshold)
VALUES ('LebeldiShop', 'shop@lebeldishop.com', '+212 5 00 00 00 00', 'Casablanca, Maroc', 'MAD', 25.00, 500.00);

INSERT INTO categories (slug, name_fr, name_ar, description_fr, description_ar, image, sort_order) VALUES
('mode-marocaine', 'Mode marocaine', 'الموضة المغربية', 'Élégance contemporaine inspirée du Maroc', 'أناقة معاصرة مستوحاة من المغرب', 'https://images.unsplash.com/photo-1529139574466-a303027c1d8b?auto=format&fit=crop&w=900&q=80', 1),
('vetements', 'Vêtements', 'ملابس', 'Tenues élégantes et confortables', 'ملابس أنيقة ومريحة', 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=900&q=80', 2),
('artisanat-marocain', 'Artisanat marocain', 'الحرف المغربية', 'Produits faits main et authentiques', 'منتجات يدوية أصلية', 'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?auto=format&fit=crop&w=900&q=80', 3),
('cosmetiques', 'Cosmétiques', 'مستحضرات تجميل', 'Beauté et bien-être naturels', 'الجمال والعناية الطبيعية', 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=900&q=80', 4),
('services', 'Services', 'خدمات', 'Prestations locales et artisanales', 'خدمات محلية وحرفية', 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=900&q=80', 5);

INSERT INTO products (sku, slug, name_fr, name_ar, description_fr, description_ar, price, old_price, stock, brand, image, category_id, is_featured, is_popular) VALUES
('LBS-001', 'djellaba-traditionnelle-bleu', 'Djellaba traditionnelle bleu', 'جلابة تقليدية زرقاء', 'Djellaba douce et élégante pour les moments raffinés.', 'جلابة ناعمة وأنيقة للمناسبات الراقية.', 420.00, 520.00, 14, 'Lebeldi', 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=900&q=80', 1, 1, 1),
('LBS-002', 'lampe-zellige-artisanale', 'Lampe zellige artisanale', 'مصباح زليج يدوي', 'Ambiance chaleureuse inspirée de la décoration marocaine.', 'جو دافئ مستوحى من ديكور المغرب.', 310.00, 390.00, 20, 'Riad Atelier', 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=900&q=80', 3, 1, 1),
('LBS-003', 'huile-dargan-pure', 'Huile d''argan pure', 'زيت الأركان النقي', 'Huile de beauté naturelle, riche en bienfaits.', 'زيت طبيعي غني بفوائده للبشرة والشعر.', 180.00, 230.00, 32, NULL, 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=900&q=80', 4, 0, 1),
('LBS-004', 'sac-en-cuir-marocain', 'Sac en cuir marocain', 'حقيبة من الجلد المغربي', 'Sac élégant avec finition artisanale.', 'حقيبة أنيقة بتفاصيل حرفية.', 640.00, 780.00, 9, NULL, 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=900&q=80', 3, 0, 1),
('LBS-005', 'tapis-beni-ourain', 'Tapis Beni Ouarain', 'سجادة بني عوران', 'Tapis traditionnel à motifs épurés.', 'سجادة تقليدية بنقوش بسيطة.', 890.00, NULL, 7, NULL, 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=900&q=80', 3, 1, 0);

INSERT INTO services (title_fr, title_ar, description_fr, description_ar, price, location, image) VALUES
('Décoration intérieure', 'ديكور داخلي', 'Concevoir un intérieur marocain lumineux et moderne.', 'تصميم داخلي مغربي أنيق وحديث.', 1800.00, 'Casablanca', 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=900&q=80'),
('Réparation & maintenance', 'إصلاح وصيانة', 'Services rapides pour ménages et boutiques.', 'خدمات سريعة للمنازل والمتاجر.', 450.00, 'Rabat', 'https://images.unsplash.com/photo-1581578731548-c64695cc6952?auto=format&fit=crop&w=900&q=80');
