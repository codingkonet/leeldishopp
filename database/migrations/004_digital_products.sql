ALTER TABLE products
    ADD COLUMN product_type ENUM('PHYSICAL','DIGITAL') NOT NULL DEFAULT 'PHYSICAL' AFTER id,
    ADD COLUMN digital_file VARCHAR(500) NULL AFTER image,
    ADD COLUMN digital_filename VARCHAR(255) NULL AFTER digital_file;