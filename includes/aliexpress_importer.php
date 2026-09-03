<?php
declare(strict_types=1);

function is_allowed_supplier_host(string $host): bool
{
    $host = strtolower(trim($host, '.'));
    return $host === 'aliexpress.com' || str_ends_with($host, '.aliexpress.com');
}

function validate_aliexpress_url(string $url): string
{
    $url = trim($url);
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        throw new RuntimeException('Enter a valid AliExpress product URL.');
    }
    $parts = parse_url($url);
    if (($parts['scheme'] ?? '') !== 'https' || !is_allowed_supplier_host((string) ($parts['host'] ?? ''))) {
        throw new RuntimeException('Only public HTTPS AliExpress URLs are accepted.');
    }
    foreach (gethostbynamel((string) $parts['host']) ?: [] as $ip) {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            throw new RuntimeException('The supplier host is not publicly routable.');
        }
    }
    return $url;
}

function fetch_aliexpress_page(string $url): string
{
    if (!function_exists('curl_init')) {
        throw new RuntimeException('PHP cURL extension is required for URL imports.');
    }
    $curl = curl_init($url);
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 3,
        CURLOPT_CONNECTTIMEOUT => 8,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_USERAGENT => 'LebeldiShop-Importer/1.0 (+authorized-catalog-import)',
        CURLOPT_HTTPHEADER => ['Accept: text/html,application/xhtml+xml'],
    ]);
    $html = curl_exec($curl);
    $status = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
    $finalUrl = (string) curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
    curl_close($curl);

    if (!is_string($html) || $status < 200 || $status >= 300 || !is_allowed_supplier_host((string) parse_url($finalUrl, PHP_URL_HOST))) {
        throw new RuntimeException('AliExpress did not return an importable public product page.');
    }
    if (strlen($html) > 8 * 1024 * 1024) {
        throw new RuntimeException('The supplier page is too large to import.');
    }
    return $html;
}

function first_meta_value(DOMXPath $xpath, string $property): string
{
    $nodes = $xpath->query('//meta[@property="' . $property . '"]/@content | //meta[@name="' . $property . '"]/@content');
    return $nodes && $nodes->length > 0 ? trim((string) $nodes->item(0)->nodeValue) : '';
}

function extract_aliexpress_product(string $url, string $html): array
{
    libxml_use_internal_errors(true);
    $document = new DOMDocument();
    $document->loadHTML($html);
    $xpath = new DOMXPath($document);
    $product = [];

    foreach ($xpath->query('//script[@type="application/ld+json"]') ?: [] as $node) {
        $decoded = json_decode((string) $node->textContent, true);
        $candidates = is_array($decoded) && isset($decoded['@graph']) ? $decoded['@graph'] : [$decoded];
        foreach ($candidates as $candidate) {
            if (!is_array($candidate)) continue;
            $type = $candidate['@type'] ?? '';
            if ($type === 'Product' || (is_array($type) && in_array('Product', $type, true))) {
                $product = $candidate;
                break 2;
            }
        }
    }

    $name = trim((string) ($product['name'] ?? '')) ?: first_meta_value($xpath, 'og:title');
    $description = trim((string) ($product['description'] ?? '')) ?: first_meta_value($xpath, 'og:description');
    $image = is_array($product['image'] ?? null) ? (string) ($product['image'][0] ?? '') : (string) ($product['image'] ?? '');
    $image = $image ?: first_meta_value($xpath, 'og:image');
    $offer = is_array($product['offers'] ?? null) && array_is_list($product['offers']) ? ($product['offers'][0] ?? []) : ($product['offers'] ?? []);
    $price = (float) ($offer['price'] ?? 0);
    $currency = strtoupper((string) ($offer['priceCurrency'] ?? ''));
    $sku = trim((string) ($product['sku'] ?? ''));
    if ($sku === '' && preg_match('#/item/(\d+)#', $url, $match)) $sku = 'ALI-' . $match[1];

    if ($name === '' || $description === '' || !filter_var($image, FILTER_VALIDATE_URL) || $price <= 0) {
        throw new RuntimeException('The public page did not expose complete product metadata. Use an official CSV/API export instead.');
    }
    if ($currency !== '' && !in_array($currency, ['MAD', 'USD', 'EUR'], true)) {
        throw new RuntimeException('Unsupported supplier currency. Convert the price before importing.');
    }
    if ($currency === 'USD') $price *= (float) (getenv('USD_TO_MAD') ?: 10);
    if ($currency === 'EUR') $price *= (float) (getenv('EUR_TO_MAD') ?: 11);

    return [
        'sku' => substr($sku ?: 'ALI-' . strtoupper(bin2hex(random_bytes(5))), 0, 60),
        'name' => substr($name, 0, 200),
        'description' => substr(strip_tags($description), 0, 5000),
        'price' => round($price, 2),
        'image' => substr($image, 0, 500),
        'source_url' => substr($url, 0, 500),
    ];
}

function import_aliexpress_url(PDO $pdo, string $url, bool $publish = false): array
{
    $url = validate_aliexpress_url($url);
    $existing = $pdo->prepare('SELECT id FROM products WHERE source_url = ? OR sku = ? LIMIT 1');
    $existing->execute([$url, 'ALI-' . (preg_match('#/item/(\d+)#', $url, $match) ? $match[1] : '')]);
    if ($existing->fetchColumn()) throw new RuntimeException('This AliExpress product is already imported.');

    $product = extract_aliexpress_product($url, fetch_aliexpress_page($url));
    $categoryId = find_or_create_category($pdo, 'AliExpress imports');
    $slug = import_slug($product['name'] . '-' . $product['sku']);
    $stmt = $pdo->prepare('INSERT INTO products (product_type, sku, slug, name_fr, name_ar, description_fr, description_ar, price, old_price, stock, brand, image, category_id, source_name, source_url, is_published) VALUES ("PHYSICAL", ?, ?, ?, ?, ?, ?, ?, NULL, 0, "AliExpress", ?, ?, "AliExpress", ?, ?)');
    $stmt->execute([$product['sku'], $slug, $product['name'], $product['name'], $product['description'], $product['description'], $product['price'], $product['image'], $categoryId, $product['source_url'], $publish ? 1 : 0]);
    return ['id' => (int) $pdo->lastInsertId(), 'slug' => $slug, 'name' => $product['name'], 'published' => $publish];
}
