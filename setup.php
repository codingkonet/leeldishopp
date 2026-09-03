<?php
declare(strict_types=1);

session_start();

if (is_file(__DIR__ . '/.installed')) {
    http_response_code(403);
    exit('LebeldiShop is already installed. Delete setup.php only after installation, then remove .installed only if you intentionally need to reinstall.');
}

$defaults = [
    'db_host' => '127.0.0.1',
    'db_port' => '3306',
    'db_name' => 'lebeldishop',
    'db_user' => 'root',
];
$data = array_merge($defaults, $_POST);
$error = '';
$success = false;

function setup_value(string $key): string
{
    global $data;
    return htmlspecialchars((string) ($data[$key] ?? ''), ENT_QUOTES, 'UTF-8');
}

function write_env_file(array $data): void
{
    $env = "DB_HOST=" . $data['db_host'] . "\n"
        . "DB_PORT=" . $data['db_port'] . "\n"
        . "DB_NAME=" . $data['db_name'] . "\n"
        . "DB_USER=" . $data['db_user'] . "\n"
        . "DB_PASS=" . $data['db_pass'] . "\n"
        . "APP_URL=" . ($data['app_url'] ?: 'http://localhost:8000') . "\n"
        . "APP_DEBUG=0\n";

    if (file_put_contents(__DIR__ . '/.env', $env, LOCK_EX) === false) {
        throw new RuntimeException('Cannot write .env. Check folder permissions.');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!hash_equals($_SESSION['setup_token'] ?? '', (string) ($_POST['setup_token'] ?? ''))) {
            throw new RuntimeException('Invalid setup token. Refresh the page and try again.');
        }

        $required = ['db_host', 'db_port', 'db_name', 'db_user', 'admin_email', 'admin_password'];
        foreach ($required as $field) {
            if (trim((string) ($_POST[$field] ?? '')) === '') {
                throw new RuntimeException('Please fill in all required fields.');
            }
        }
        if (!filter_var($_POST['admin_email'], FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('The admin email is invalid.');
        }
        if (strlen((string) $_POST['admin_password']) < 8) {
            throw new RuntimeException('The admin password must contain at least 8 characters.');
        }
        if (!preg_match('/^[a-zA-Z0-9_]+$/', (string) $_POST['db_name'])) {
            throw new RuntimeException('Database name may only contain letters, numbers, and underscores.');
        }

        $dbDsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $_POST['db_host'], $_POST['db_port'], $_POST['db_name']);
        try {
            $pdo = new PDO($dbDsn, (string) $_POST['db_user'], (string) $_POST['db_pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        } catch (PDOException) {
            $serverDsn = sprintf('mysql:host=%s;port=%s;charset=utf8mb4', $_POST['db_host'], $_POST['db_port']);
            $serverPdo = new PDO($serverDsn, (string) $_POST['db_user'], (string) $_POST['db_pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            $serverPdo->exec('CREATE DATABASE IF NOT EXISTS `' . $_POST['db_name'] . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
            $pdo = new PDO($dbDsn, (string) $_POST['db_user'], (string) $_POST['db_pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        }

        $schema = file_get_contents(__DIR__ . '/database/schema.sql');
        if ($schema === false) {
            throw new RuntimeException('database/schema.sql could not be read.');
        }
        $pdo->exec($schema);

        $hash = password_hash((string) $_POST['admin_password'], PASSWORD_BCRYPT);
        $adminStmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $adminStmt->execute([strtolower(trim((string) $_POST['admin_email']))]);
        $existing = $adminStmt->fetch();
        if ($existing) {
            $update = $pdo->prepare('UPDATE users SET password_hash = ?, role = "SUPER_ADMIN", is_active = 1 WHERE id = ?');
            $update->execute([$hash, $existing['id']]);
        } else {
            $insert = $pdo->prepare('INSERT INTO users (name, email, password_hash, role, is_active) VALUES (?, ?, ?, "SUPER_ADMIN", 1)');
            $insert->execute(['LebeldiShop Admin', strtolower(trim((string) $_POST['admin_email'])), $hash]);
        }

        write_env_file($_POST);
        file_put_contents(__DIR__ . '/.installed', date('c'), LOCK_EX);
        $success = true;
    } catch (Throwable $exception) {
        $error = $exception->getMessage();
    }
}

$_SESSION['setup_token'] ??= bin2hex(random_bytes(32));
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LebeldiShop Setup</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<main class="container page-content">
    <div class="panel" style="max-width:720px;margin:2rem auto;">
        <p style="color:var(--brand);font-weight:800;">LEBELDISHOP</p>
        <h1>Installation Setup</h1>
        <p class="muted">Enter your MySQL connection and create the first administrator account.</p>

        <?php if ($success): ?>
            <div class="alert alert-success">Installation complete. Delete <strong>setup.php</strong> now, then sign in with your administrator account.</div>
            <a class="btn btn-brand" href="index.php">Open storefront</a>
        <?php else: ?>
            <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
            <form method="post" class="form-grid cols-2">
                <input type="hidden" name="setup_token" value="<?= htmlspecialchars($_SESSION['setup_token'], ENT_QUOTES, 'UTF-8') ?>">
                <h2 style="grid-column:span 2;">Database</h2>
                <input name="db_host" required value="<?= setup_value('db_host') ?>" placeholder="MySQL host">
                <input name="db_port" required value="<?= setup_value('db_port') ?>" placeholder="Port">
                <input name="db_name" required value="<?= setup_value('db_name') ?>" placeholder="Database name">
                <input name="db_user" required value="<?= setup_value('db_user') ?>" placeholder="Database user">
                <input name="db_pass" type="password" value="<?= setup_value('db_pass') ?>" placeholder="Database password">
                <input name="app_url" value="<?= setup_value('app_url') ?>" placeholder="Application URL">
                <h2 style="grid-column:span 2;">SUPER_ADMIN account</h2>
                <input name="admin_email" type="email" required value="<?= setup_value('admin_email') ?>" placeholder="Admin email">
                <input name="admin_password" type="password" required minlength="8" placeholder="Password (8+ characters)">
                <button class="btn btn-brand" type="submit" style="grid-column:span 2;">Install LebeldiShop</button>
            </form>
        <?php endif; ?>
    </div>
</main>
</body>
</html>
