<?php
declare(strict_types=1);

// Loads simple KEY=value environment files without requiring Composer.
$envFile = __DIR__ . '/../.env';
if (is_file($envFile)) {
	foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
		$line = trim($line);
		if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
			continue;
		}
		[$key, $value] = explode('=', $line, 2);
		$key = trim($key);
		$value = trim($value);
		if ($key !== '' && getenv($key) === false) {
			putenv("{$key}={$value}");
		}
	}
}

// Environment-driven configuration; falls back to local defaults for development only.
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'lebeldishop');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');

define('APP_NAME', 'LebeldiShop');
define('APP_URL', getenv('APP_URL') ?: 'http://localhost:8000');
define('DEFAULT_CURRENCY', 'MAD');
define('FREE_SHIPPING_THRESHOLD', 500.00);
define('DEFAULT_SHIPPING_FEES', ['STANDARD' => 25.00, 'EXPRESS' => 45.00, 'PICKUP' => 0.00]);

define('SESSION_NAME', 'lebeldishop_session');

error_reporting(E_ALL);
ini_set('display_errors', getenv('APP_DEBUG') === '1' ? '1' : '0');
