<?php
declare(strict_types=1);

// Resolves the app's web root regardless of whether the current script lives
// at the domain root or one level deep in /account or /admin.
function base_path(): string
{
    static $base = null;
    if ($base !== null) {
        return $base;
    }
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    $base = preg_replace('#/(account|admin)$#', '', $scriptDir);
    $base = rtrim((string) $base, '/');
    return $base;
}

function href_page(string $path): string
{
    return base_path() . '/' . ltrim($path, '/');
}

function href_asset(string $path): string
{
    return base_path() . '/' . ltrim($path, '/');
}

function start_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_name(SESSION_NAME);
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function current_lang(): string
{
    if (isset($_GET['lang']) && in_array($_GET['lang'], ['fr', 'ar'], true)) {
        $_SESSION['lang'] = $_GET['lang'];
    }
    return $_SESSION['lang'] ?? 'fr';
}

function load_dictionary(string $lang): array
{
    $file = __DIR__ . "/lang/{$lang}.php";
    if (!is_file($file)) {
        $file = __DIR__ . '/lang/fr.php';
    }
    return require $file;
}

function t(string $key): string
{
    global $dict;
    return $dict[$key] ?? $key;
}

function store_settings(PDO $pdo): array
{
    $settings = $pdo->query('SELECT * FROM settings ORDER BY id LIMIT 1')->fetch();
    return $settings ?: [
        'shop_name' => APP_NAME,
        'email' => 'shop@lebeldishop.com',
        'currency' => DEFAULT_CURRENCY,
        'delivery_fee' => DEFAULT_SHIPPING_FEES['STANDARD'],
        'free_shipping_threshold' => FREE_SHIPPING_THRESHOLD,
        'slogan_fr' => 'Le meilleur du Maroc, chez vous',
        'slogan_ar' => 'أفضل المنتجات المغربية، إلى منزلك',
        'primary_color' => '#b47d2d',
        'accent_color' => '#1f2937',
        'theme_name' => 'atlas',
    ];
}

function valid_hex_color(string $color, string $fallback): string
{
    return preg_match('/^#[0-9a-fA-F]{6}$/', $color) ? $color : $fallback;
}

function upload_brand_asset(array $upload, string $kind): ?string
{
    if (($upload['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if (($upload['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK || ($upload['size'] ?? 0) > 5 * 1024 * 1024) {
        throw new RuntimeException('The image upload failed or exceeds the 5 MB limit.');
    }

    $imageInfo = @getimagesize((string) $upload['tmp_name']);
    $allowed = ['image/png' => 'png', 'image/jpeg' => 'jpg', 'image/webp' => 'webp', 'image/x-icon' => 'ico', 'image/vnd.microsoft.icon' => 'ico'];
    $mime = $imageInfo['mime'] ?? '';
    if (!$imageInfo || !isset($allowed[$mime])) {
        throw new RuntimeException("The {$kind} must be a PNG, JPG, WEBP, or ICO image.");
    }
    if ($kind === 'favicon' && (($imageInfo[0] ?? 0) > 512 || ($imageInfo[1] ?? 0) > 512)) {
        throw new RuntimeException('The favicon must be no larger than 512x512 pixels.');
    }

    $directory = dirname(__DIR__) . '/assets/uploads';
    if (!is_dir($directory) && !mkdir($directory, 0750, true) && !is_dir($directory)) {
        throw new RuntimeException('Cannot create the upload directory.');
    }
    $filename = $kind . '-' . bin2hex(random_bytes(12)) . '.' . $allowed[$mime];
    $destination = $directory . '/' . $filename;
    if (!move_uploaded_file((string) $upload['tmp_name'], $destination)) {
        throw new RuntimeException('Cannot save the uploaded image.');
    }

    return href_asset('assets/uploads/' . $filename);
}

function upload_digital_asset(array $upload): ?array
{
    if (($upload['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
    if (($upload['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK || ($upload['size'] ?? 0) > 50 * 1024 * 1024) {
        throw new RuntimeException('The digital file upload failed or exceeds the 50 MB limit.');
    }
    $allowed = ['application/pdf' => 'pdf', 'application/zip' => 'zip', 'application/epub+zip' => 'epub'];
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file((string) $upload['tmp_name']);
    if (!isset($allowed[$mime])) throw new RuntimeException('Digital files must be PDF, ZIP, or EPUB.');

    $directory = dirname(__DIR__) . '/storage/digital';
    if (!is_dir($directory) && !mkdir($directory, 0750, true) && !is_dir($directory)) throw new RuntimeException('Cannot create digital storage.');
    $filename = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
    if (!move_uploaded_file((string) $upload['tmp_name'], $directory . '/' . $filename)) throw new RuntimeException('Cannot save digital file.');
    return ['path' => $directory . '/' . $filename, 'filename' => basename((string) $upload['name'])];
}

function format_price(float $value, string $lang = 'fr'): string
{
    $formatted = number_format($value, 0, ',', ' ');
    return $lang === 'ar' ? "{$formatted} د.م" : "{$formatted} DH";
}

function redirect(string $path): never
{
    header("Location: {$path}");
    exit;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', (string) $token)) {
        http_response_code(403);
        exit('Invalid CSRF token.');
    }
}

function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }
    $value = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $value;
}

function get_order_status_label(string $status, string $lang): string
{
    $labels = [
        'NEW' => ['fr' => 'Nouvelle', 'ar' => 'جديدة'],
        'CONFIRMED' => ['fr' => 'Confirmée', 'ar' => 'مؤكدة'],
        'PREPARING' => ['fr' => 'En préparation', 'ar' => 'قيد التجهيز'],
        'SHIPPED' => ['fr' => 'Expédiée', 'ar' => 'تم الشحن'],
        'IN_DELIVERY' => ['fr' => 'En livraison', 'ar' => 'قيد التوصيل'],
        'DELIVERED' => ['fr' => 'Livrée', 'ar' => 'تم التوصيل'],
        'CANCELLED' => ['fr' => 'Annulée', 'ar' => 'ملغاة'],
        'RETURNED' => ['fr' => 'Retour', 'ar' => 'مرتجع'],
        'DELIVERY_FAILED' => ['fr' => 'Échec de livraison', 'ar' => 'فشل التوصيل'],
    ];
    return $labels[$status][$lang] ?? $status;
}

function generate_order_number(): string
{
    return 'LBS-' . substr((string) time(), -8) . '-' . random_int(10, 99);
}
