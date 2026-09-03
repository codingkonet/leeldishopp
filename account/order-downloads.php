<?php
require __DIR__ . '/../includes/bootstrap.php';
require_login(href_page('account/login.php'));
$user = current_user();
$orderNumber = trim((string) ($_GET['order'] ?? ''));

$stmt = $pdo->prepare('SELECT p.id, p.name_fr, p.name_ar, p.digital_filename, o.order_number, o.status, o.payment_status FROM orders o JOIN order_items oi ON oi.order_id = o.id JOIN products p ON p.id = oi.product_id WHERE o.order_number = ? AND o.user_id = ? AND p.product_type = "DIGITAL"');
$stmt->execute([$orderNumber, $user['id']]);
$files = $stmt->fetchAll();
$available = static fn (array $file): bool => $file['status'] === 'DELIVERED' || $file['payment_status'] === 'PAID';

require __DIR__ . '/../includes/header.php';
?>
<h1><?= $lang === 'fr' ? 'Téléchargements' : 'التنزيلات' ?></h1>
<div class="panel" style="margin-top:1.5rem;">
<?php foreach ($files as $file): ?>
    <div style="display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);padding:1rem 0;gap:1rem;">
        <strong><?= e($lang === 'fr' ? $file['name_fr'] : $file['name_ar']) ?></strong>
        <?php if ($available($file)): ?><a class="btn btn-dark" href="<?= href_page('download.php?order=' . urlencode($file['order_number']) . '&product=' . (int) $file['id']) ?>">↓ <?= $lang === 'fr' ? 'Télécharger' : 'تنزيل' ?></a><?php else: ?><span class="muted"><?= $lang === 'fr' ? 'Disponible après livraison/paiement' : 'متاح بعد التوصيل أو الدفع' ?></span><?php endif; ?>
    </div>
<?php endforeach; ?>
<?php if (!$files): ?><p class="muted"><?= $lang === 'fr' ? 'Aucun fichier numérique pour cette commande.' : 'لا توجد ملفات رقمية لهذا الطلب.' ?></p><?php endif; ?>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
