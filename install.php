<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    exit("Run this installer from the command line.\n");
}

if (is_file(__DIR__ . '/.installed')) {
    exit("LebeldiShop is already installed. Remove .installed only if you intentionally need to reinstall.\n");
}

fwrite(STDOUT, "LebeldiShop PHP installer\n\n");
function ask(string $label, string $default = ''): string
{
    fwrite(STDOUT, $label . ($default !== '' ? " [{$default}]" : '') . ': ');
    $value = trim((string) fgets(STDIN));
    return $value !== '' ? $value : $default;
}

$host = ask('MySQL host', '127.0.0.1');
$port = ask('MySQL port', '3306');
$name = ask('Database name', 'lebeldishop');
$user = ask('MySQL user', 'root');
$pass = ask('MySQL password');
$email = ask('SUPER_ADMIN email', 'shop@lebeldishop.com');
$password = ask('SUPER_ADMIN password (8+ characters)');

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
    exit("Invalid email or password.\n");
}
if (!preg_match('/^[a-zA-Z0-9_]+$/', $name)) {
    exit("Invalid database name.\n");
}

try {
    $server = new PDO("mysql:host={$host};port={$port};charset=utf8mb4", $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $server->exec("CREATE DATABASE IF NOT EXISTS `{$name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4", $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $schema = file_get_contents(__DIR__ . '/database/schema.sql');
    if ($schema === false) throw new RuntimeException('Cannot read database/schema.sql');
    $pdo->exec($schema);

    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash, role, is_active) VALUES (?, ?, ?, "SUPER_ADMIN", 1) ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), role = "SUPER_ADMIN", is_active = 1');
    $stmt->execute(['LebeldiShop Admin', strtolower($email), $hash]);

    $env = "DB_HOST={$host}\nDB_PORT={$port}\nDB_NAME={$name}\nDB_USER={$user}\nDB_PASS={$pass}\nAPP_URL=http://localhost:8000\nAPP_DEBUG=0\n";
    file_put_contents(__DIR__ . '/.env', $env, LOCK_EX);
    file_put_contents(__DIR__ . '/.installed', date('c'), LOCK_EX);
    fwrite(STDOUT, "\nInstallation complete. Start with: php -S localhost:8000\n");
} catch (Throwable $exception) {
    fwrite(STDERR, "Installation failed: {$exception->getMessage()}\n");
    exit(1);
}
