<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/cart.php';
require_once __DIR__ . '/commerce.php';
require_once __DIR__ . '/email.php';

start_session();

$lang = current_lang();
$dict = load_dictionary($lang);
$isRtl = $lang === 'ar';
try {
	$pdo = get_pdo();
	$settings = store_settings($pdo);
} catch (Throwable $exception) {
	http_response_code(503);
	$setupUrl = base_path() . '/setup.php';
	$message = htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8');
	exit("<!doctype html><html lang=\"{$lang}\" dir=\"" . ($isRtl ? 'rtl' : 'ltr') . "\"><head><meta charset=\"utf-8\"><meta name=\"viewport\" content=\"width=device-width,initial-scale=1\"><title>LebeldiShop setup</title><style>body{font-family:Arial,sans-serif;background:#f8fafc;color:#1f2937;margin:0;padding:2rem}.box{max-width:680px;margin:10vh auto;background:#fff;border:1px solid #e2e8f0;border-radius:18px;padding:2rem;box-shadow:0 4px 20px #0000000d}a{display:inline-block;background:#b47d2d;color:#fff;padding:.75rem 1rem;border-radius:999px;text-decoration:none}small{display:block;color:#64748b;margin-top:1rem;word-break:break-word}</style></head><body><div class=\"box\"><h1>LebeldiShop</h1><h2>Database setup required</h2><p>Configure MySQL, then complete the installation wizard.</p><a href=\"{$setupUrl}\">Open setup</a><small>{$message}</small></div></body></html>");
}
