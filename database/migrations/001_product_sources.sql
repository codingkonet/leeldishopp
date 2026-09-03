ALTER TABLE products
    ADD COLUMN source_name VARCHAR(120) NULL AFTER category_id,
    ADD COLUMN source_url VARCHAR(500) NULL AFTER source_name;