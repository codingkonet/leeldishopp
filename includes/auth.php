<?php
declare(strict_types=1);

const MAX_LOGIN_ATTEMPTS = 5;
const LOGIN_LOCK_MINUTES = 15;
const ADMIN_ROLES = ['SUPER_ADMIN', 'ADMIN', 'MANAGER', 'ORDER_MANAGER', 'PRODUCT_MANAGER'];

function current_user(): ?array
{
    static $cache = null;
    static $resolved = false;

    if ($resolved) {
        return $cache;
    }
    $resolved = true;

    if (empty($_SESSION['user_id'])) {
        return null;
    }

    $stmt = get_pdo()->prepare('SELECT id, name, email, role, phone, is_active FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    $cache = $user ?: null;

    return $cache;
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

function is_admin(): bool
{
    $user = current_user();
    return $user !== null && in_array($user['role'], ADMIN_ROLES, true);
}

function require_login(string $redirectTo): void
{
    if (!is_logged_in()) {
        redirect($redirectTo);
    }
}

function require_admin(string $redirectTo): void
{
    if (!is_admin()) {
        redirect($redirectTo);
    }
}

function attempt_login(string $email, string $password): array
{
    $pdo = get_pdo();
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        return ['success' => false, 'error' => 'invalid_credentials'];
    }

    if (!empty($user['locked_until']) && strtotime((string) $user['locked_until']) > time()) {
        return ['success' => false, 'error' => 'locked'];
    }

    if (!(int) $user['is_active']) {
        return ['success' => false, 'error' => 'inactive'];
    }

    if (!password_verify($password, $user['password_hash'])) {
        $attempts = (int) $user['failed_attempts'] + 1;
        $lockedUntil = null;
        if ($attempts >= MAX_LOGIN_ATTEMPTS) {
            $lockedUntil = date('Y-m-d H:i:s', time() + LOGIN_LOCK_MINUTES * 60);
            $attempts = 0;
        }
        $update = $pdo->prepare('UPDATE users SET failed_attempts = ?, locked_until = ? WHERE id = ?');
        $update->execute([$attempts, $lockedUntil, $user['id']]);
        return ['success' => false, 'error' => 'invalid_credentials'];
    }

    $reset = $pdo->prepare('UPDATE users SET failed_attempts = 0, locked_until = NULL WHERE id = ?');
    $reset->execute([$user['id']]);

    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];

    return ['success' => true, 'user' => $user];
}

function register_user(string $name, string $email, string $password, ?string $phone): array
{
    $pdo = get_pdo();
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        return ['success' => false, 'error' => 'email_taken'];
    }

    $hash = password_hash($password, PASSWORD_BCRYPT);
    $insert = $pdo->prepare('INSERT INTO users (name, email, password_hash, phone, role, is_active) VALUES (?, ?, ?, ?, "CUSTOMER", 1)');
    $insert->execute([$name, $email, $hash, $phone]);

    return ['success' => true, 'id' => (int) $pdo->lastInsertId()];
}

function logout_user(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}
