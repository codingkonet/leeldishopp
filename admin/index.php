<?php
require __DIR__ . '/includes/admin_header.php';

$revenue = (float) $pdo->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE status != 'CANCELLED'")->fetchColumn();
$orderCount = (int) $pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn();
$productCount = (int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$customerCount = (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'CUSTOMER'")->fetchColumn();
$pendingCount = (int) $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'NEW'")->fetchColumn();
$recentOrders = $pdo->query('SELECT order_number, customer_name, total, status, created_at FROM orders ORDER BY created_at DESC LIMIT 6')->fetchAll();
?>

<div style="display:flex; justify-content:space-between; align-items:flex-end; flex-wrap:wrap; gap:1rem; margin-bottom:1.5rem;">
    <div>
        <p style="color:var(--brand); font-weight:700; font-size:.85rem;">ADMINISTRATION</p>
        <h1><?= $lang === 'fr' ? 'Tableau de bord' : 'لوحة التحكم' ?></h1>
    </div>
    <span class="badge" style="background:#fef3c7; color:#92400e;"><?= $pendingCount ?> <?= $lang === 'fr' ? 'commande(s) à confirmer' : 'طلب بانتظار التأكيد' ?></span>
</div>

<div class="stat-grid">
    <div class="stat-card"><p class="muted"><?= $lang === 'fr' ? 'Chiffre d’affaires' : 'الإيرادات' ?></p><p class="value"><?= format_price($revenue, $lang) ?></p></div>
    <div class="stat-card"><p class="muted"><?= $lang === 'fr' ? 'Commandes' : 'الطلبات' ?></p><p class="value"><?= $orderCount ?></p></div>
    <div class="stat-card"><p class="muted"><?= $lang === 'fr' ? 'Produits' : 'المنتجات' ?></p><p class="value"><?= $productCount ?></p></div>
    <div class="stat-card"><p class="muted"><?= $lang === 'fr' ? 'Clients' : 'العملاء' ?></p><p class="value"><?= $customerCount ?></p></div>
</div>

<div class="panel" style="margin-bottom:1.5rem; border-color:#f0d69f; background:#fffaf0;">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap;">
        <div><h2 style="margin:0;"><?= $lang === 'fr' ? 'Importer depuis AliExpress' : 'الاستيراد من علي إكسبريس' ?></h2><p class="muted"><?= $lang === 'fr' ? 'Ajoutez un produit avec son URL publique, puis vérifiez-le avant publication.' : 'أضف منتجاً عبر رابطه العام ثم راجعه قبل النشر.' ?></p></div>
        <a class="btn btn-brand" href="<?= href_page('admin/import-aliexpress.php') ?>">↓ <?= $lang === 'fr' ? 'Importer une URL' : 'استيراد رابط' ?></a>
    </div>
</div>

<div class="panel">
    <h2><?= $lang === 'fr' ? 'Dernières commandes' : 'أحدث الطلبات' ?></h2>
    <table style="margin-top:1rem;">
        <thead><tr><th><?= $lang === 'fr' ? 'Commande' : 'الطلب' ?></th><th><?= $lang === 'fr' ? 'Client' : 'العميل' ?></th><th><?= $lang === 'fr' ? 'Statut' : 'الحالة' ?></th><th><?= e(t('total')) ?></th></tr></thead>
        <tbody>
            <?php foreach ($recentOrders as $order): ?>
                <tr>
                    <td><strong><?= e($order['order_number']) ?></strong></td>
                    <td><?= e($order['customer_name']) ?></td>
                    <td><span class="badge" style="background:#fef3c7; color:#92400e;"><?= e($order['status']) ?></span></td>
                    <td><?= format_price((float) $order['total'], $lang) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($recentOrders)): ?>
                <tr><td colspan="4" class="muted" style="text-align:center;"><?= $lang === 'fr' ? 'Aucune commande pour le moment.' : 'لا توجد طلبات بعد.' ?></td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
