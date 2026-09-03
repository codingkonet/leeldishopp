<?php
declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';
require_login(href_page('account/login.php'));

$orderNumber = trim((string) ($_GET['order'] ?? ''));
$productId = (int) ($_GET['product'] ?? 0);
$user = current_user();

$stmt = $pdo->prepare('SELECT p.digital_file, p.digital_filename, o.status, o.payment_status FROM orders o JOIN order_items oi ON oi.order_id = o.id JOIN products p ON p.id = oi.product_id WHERE o.order_number = ? AND p.id = ? AND o.user_id = ? AND p.product_type = "DIGITAL" LIMIT 1');
$stmt->execute([$orderNumber, $productId, $user['id']]);
$file = $stmt->fetch();

if (!$file || (!in_array($file['status'], ['DELIVERED'], true) && $file['payment_status'] !== 'PAID') || !is_file((string) $file['digital_file'])) {
    http_response_code(403);
    exit('This digital download is not available.');
}

$path = (string) $file['digital_file'];
$mime = (new finfo(FILEINFO_MIME_TYPE))->file($path) ?: 'application/octet-stream';
header('Content-Type: ' . $mime);
header('Content-Disposition: attachment; filename="' . str_replace(['"', "\r", "\n"], '', (string) $file['digital_filename']) . '"');
header('Content-Length: ' . (string) filesize($path));
readfile($path);
