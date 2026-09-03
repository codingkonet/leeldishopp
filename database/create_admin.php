<?php
declare(strict_types=1);

// CLI-only script to safely create/reset the SUPER_ADMIN account without hardcoding a password.
// Usage: php database/create_admin.php

if (php_sapi_name() !== 'cli') {
    exit('This script must be run from the command line.');
}

require __DIR__ . '/../config/app.php';
require __DIR__ . '/../config/database.php';

fwrite(STDOUT, "Admin email [shop@lebeldishop.com]: ");
$email = trim((string) fgets(STDIN));
$email = $email !== '' ? $email : 'shop@lebeldishop.com';

fwrite(STDOUT, "Admin password (min 8 characters): ");
$password = trim((string) fgets(STDIN));

if (strlen($password) < 8) {
    exit("Password must be at least 8 characters.\n");
}

$pdo = get_pdo();
$hash = password_hash($password, PASSWORD_BCRYPT);

$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$existing = $stmt->fetch();

if ($existing) {
    $update = $pdo->prepare('UPDATE users SET password_hash = ?, role = "SUPER_ADMIN", is_active = 1 WHERE id = ?');
    $update->execute([$hash, $existing['id']]);
    fwrite(STDOUT, "Updated existing account to SUPER_ADMIN: {$email}\n");
    exit;
}

$insert = $pdo->prepare('INSERT INTO users (name, email, password_hash, role, is_active) VALUES (?, ?, ?, "SUPER_ADMIN", 1)');
$insert->execute(['LebeldiShop Admin', $email, $hash]);
fwrite(STDOUT, "Created SUPER_ADMIN account: {$email}\n");
