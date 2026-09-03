<?php
require __DIR__ . '/includes/bootstrap.php';

$order = null;
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $number = trim((string) ($_POST['order_number'] ?? ''));
    $phone = trim((string) ($_POST['phone'] ?? ''));
    $stmt = $pdo->prepare('SELECT order_number, customer_name, city, status, payment_status, total, created_at FROM orders WHERE order_number = ? AND customer_phone = ? LIMIT 1');
    $stmt->execute([$number, $phone]);
    $order = $stmt->fetch();
    if (!$order) $error = $lang === 'fr' ? 'Commande introuvable. Vérifiez le numéro et le téléphone.' : 'الطلب غير موجود. تحقق من الرقم والهاتف.';
}

require __DIR__ . '/includes/header.php';
?>
<h1><?= $lang === 'fr' ? 'Suivi de commande' : 'تتبع الطلب' ?></h1>
<div class="panel" style="max-width:560px;margin:1.5rem auto;">
    <p class="muted"><?= $lang === 'fr' ? 'Entrez votre numéro de commande et le téléphone utilisé au checkout.' : 'أدخل رقم الطلب والهاتف المستخدم عند الطلب.' ?></p>
    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
    <form method="post" class="form-grid" style="margin-top:1rem;">
        <?= csrf_field() ?>
        <input name="order_number" required placeholder="LBS-XXXXXXXX-XX">
        <input name="phone" required placeholder="<?= $lang === 'fr' ? 'Téléphone' : 'الهاتف' ?>">
        <button class="btn btn-dark" type="submit"><?= $lang === 'fr' ? 'Rechercher' : 'بحث' ?></button>
    </form>
    <?php if ($order): ?>
        <div class="panel" style="margin-top:1rem;background:#f8fafc;">
            <p style="display:flex;justify-content:space-between;"><span><?= $lang === 'fr' ? 'Commande' : 'الطلب' ?></span><strong><?= e($order['order_number']) ?></strong></p>
            <p style="display:flex;justify-content:space-between;"><span><?= $lang === 'fr' ? 'Statut' : 'الحالة' ?></span><strong><?= e(get_order_status_label($order['status'], $lang)) ?></strong></p>
            <p style="display:flex;justify-content:space-between;"><span><?= e(t('total')) ?></span><strong><?= format_price((float) $order['total'], $lang) ?></strong></p>
        </div>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
