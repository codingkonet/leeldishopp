ALTER TABLE settings
    ADD COLUMN slogan_fr VARCHAR(255) NOT NULL DEFAULT 'Le meilleur du Maroc, chez vous',
    ADD COLUMN slogan_ar VARCHAR(255) NOT NULL DEFAULT 'أفضل المنتجات المغربية، إلى منزلك',
    ADD COLUMN primary_color VARCHAR(7) NOT NULL DEFAULT '#b47d2d',
    ADD COLUMN accent_color VARCHAR(7) NOT NULL DEFAULT '#1f2937',
    ADD COLUMN logo_url VARCHAR(500) NULL,
    ADD COLUMN favicon_url VARCHAR(500) NULL,
    ADD COLUMN theme_name VARCHAR(40) NOT NULL DEFAULT 'atlas',
    ADD COLUMN maintenance_mode TINYINT(1) NOT NULL DEFAULT 0,
    ADD COLUMN seo_title VARCHAR(255) NULL,
    ADD COLUMN seo_description VARCHAR(500) NULL;