<?php
declare(strict_types=1);

function normalize_import_header(string $header): string
{
    $header = strtolower(trim($header));
    $header = preg_replace('/[^a-z0-9]+/', '_', $header) ?? '';
    return trim($header, '_');
}

function import_header_aliases(): array
{
    return [
        'sku' => ['sku', 'seller_sku', 'product_id', 'item_id', 'reference'],
        'name_fr' => ['name_fr', 'title_fr', 'name', 'title', 'product_name', 'product_title'],
        'name_ar' => ['name_ar', 'arabic_name', 'title_ar'],
        'description_fr' => ['description_fr', 'description', 'product_description'],
        'description_ar' => ['description_ar', 'arabic_description'],
        'price' => ['price', 'sale_price', 'selling_price', 'cost', 'price_mad'],
        'old_price' => ['old_price', 'regular_price', 'compare_at_price'],
        'stock' => ['stock', 'quantity', 'inventory', 'available_quantity'],
        'brand' => ['brand', 'manufacturer'],
        'image' => ['image', 'image_url', 'main_image', 'product_image'],
        'category' => ['category', 'category_name', 'category_slug'],
        'source_url' => ['source_url', 'product_url', 'url', 'link'],
        'source_name' => ['source_name', 'source', 'supplier', 'vendor'],
    ];
}

function find_import_column(array $headers, array $aliases): ?int
{
    foreach ($aliases as $alias) {
        $index = array_search($alias, $headers, true);
        if ($index !== false) {
            return $index;
        }
    }
    return null;
}

function import_slug(string $value): string
{
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9]+/', '-', iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value) ?: '') ?? '';
    return trim($value, '-') ?: 'product-' . bin2hex(random_bytes(4));
}

function import_number(string $value, float $default = 0): float
{
    $value = trim(str_replace([' ', ','], ['', '.'], $value));
    return is_numeric($value) ? (float) $value : $default;
}

function find_or_create_category(PDO $pdo, string $category): int
{
    $category = trim($category);
    if ($category === '') {
        $category = 'Made in Morocco';
    }
    $slug = import_slug($category);
    $stmt = $pdo->prepare('SELECT id FROM categories WHERE slug = ? LIMIT 1');
    $stmt->execute([$slug]);
    $existing = $stmt->fetchColumn();
    if ($existing) {
        return (int) $existing;
    }

    $insert = $pdo->prepare('INSERT INTO categories (slug, name_fr, name_ar, description_fr, description_ar, sort_order, is_active) VALUES (?, ?, ?, ?, ?, 99, 1)');
    $insert->execute([$slug, $category, $category, 'Imported product category', 'فئة منتجات مستوردة']);
    return (int) $pdo->lastInsertId();
}

function import_products_csv(PDO $pdo, string $filePath, string $sourceName, bool $publish = false): array
{
    $handle = fopen($filePath, 'rb');
    if ($handle === false) {
        throw new RuntimeException('The CSV file could not be opened.');
    }

    $rawHeaders = fgetcsv($handle);
    if ($rawHeaders === false) {
        fclose($handle);
        throw new RuntimeException('The CSV file is empty.');
    }

    $headers = array_map(static fn ($header): string => normalize_import_header((string) $header), $rawHeaders);
    $aliases = import_header_aliases();
    $columns = [];
    foreach ($aliases as $field => $fieldAliases) {
        $columns[$field] = find_import_column($headers, $fieldAliases);
    }

    foreach (['name_fr', 'price', 'image'] as $required) {
        if ($columns[$required] === null) {
            fclose($handle);
            throw new RuntimeException("Missing required CSV column: {$required}");
        }
    }

    $result = ['created' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => []];
    $rowNumber = 1;
    $pdo->beginTransaction();

    try {
        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;
            if (count(array_filter($row, static fn ($value): bool => trim((string) $value) !== '')) === 0) {
                continue;
            }

            try {
                $value = static fn (string $field): string => $columns[$field] === null ? '' : trim((string) ($row[$columns[$field]] ?? ''));
                $nameFr = $value('name_fr');
                $price = import_number($value('price'));
                $image = $value('image');
                if ($nameFr === '' || $price <= 0 || !filter_var($image, FILTER_VALIDATE_URL)) {
                    throw new RuntimeException('name_fr, price, and a valid image URL are required.');
                }

                $sku = $value('sku') ?: 'IMP-' . strtoupper(bin2hex(random_bytes(5)));
                $nameAr = $value('name_ar') ?: $nameFr;
                $slug = import_slug($nameFr . '-' . $sku);
                $categoryId = find_or_create_category($pdo, $value('category'));
                $source = $value('source_name') ?: $sourceName;
                $sourceUrl = $value('source_url');
                $descriptionFr = $value('description_fr') ?: $nameFr;
                $descriptionAr = $value('description_ar') ?: $nameAr;
                $oldPrice = $value('old_price') !== '' ? import_number($value('old_price')) : null;
                $stock = max(0, (int) import_number($value('stock'), 0));
                $brand = $value('brand') ?: $source;

                $existingStmt = $pdo->prepare('SELECT id FROM products WHERE sku = ? LIMIT 1');
                $existingStmt->execute([$sku]);
                $existingId = $existingStmt->fetchColumn();

                if ($existingId) {
                    $update = $pdo->prepare('UPDATE products SET name_fr=?, name_ar=?, description_fr=?, description_ar=?, price=?, old_price=?, stock=?, brand=?, image=?, category_id=?, source_name=?, source_url=?, is_published=? WHERE id=?');
                    $update->execute([$nameFr, $nameAr, $descriptionFr, $descriptionAr, $price, $oldPrice, $stock, $brand, $image, $categoryId, $source, $sourceUrl ?: null, $publish ? 1 : 0, $existingId]);
                    $result['updated']++;
                } else {
                    $insert = $pdo->prepare('INSERT INTO products (sku, slug, name_fr, name_ar, description_fr, description_ar, price, old_price, stock, brand, image, category_id, source_name, source_url, is_published) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
                    $insert->execute([$sku, $slug, $nameFr, $nameAr, $descriptionFr, $descriptionAr, $price, $oldPrice, $stock, $brand, $image, $categoryId, $source, $sourceUrl ?: null, $publish ? 1 : 0]);
                    $result['created']++;
                }
            } catch (Throwable $exception) {
                $result['skipped']++;
                $result['errors'][] = "Row {$rowNumber}: {$exception->getMessage()}";
            }
        }
        fclose($handle);
        $pdo->commit();
    } catch (Throwable $exception) {
        fclose($handle);
        $pdo->rollBack();
        throw $exception;
    }

    return $result;
}
