<?php
require __DIR__ . '/../includes/bootstrap.php';
require_admin(href_page('account/login.php'));

$validStatuses = ['NEW','CONFIRMED','PREPARING','SHIPPED','IN_DELIVERY','DELIVERED','CANCELLED','RETURNED','DELIVERY_FAILED'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $orderId = (int) ($_POST['order_id'] ?? 0);
    $status = $_POST['status'] ?? '';

    if ($orderId > 0 && in_array($status, $validStatuses, true)) {
        $stmt = $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?');
        $stmt->execute([$status, $orderId]);
        flash('success', $lang === 'fr' ? 'Statut mis à jour.' : 'تم تحديث الحالة.');
    }
    redirect(href_page('admin/orders.php'));
}

$statusFilter = $_GET['status'] ?? '';
$sql = 'SELECT * FROM orders';
$params = [];
if (in_array($statusFilter, $validStatuses, true)) {
    $sql .= ' WHERE status = ?';
    $params[] = $statusFilter;
}
$sql .= ' ORDER BY created_at DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();

require __DIR__ . '/includes/admin_header.php';
?>

<h1><?= $lang === 'fr' ? 'Commandes' : 'الطلبات' ?></h1>

<div style="display:flex; gap:.5rem; flex-wrap:wrap; margin:1rem 0;">
    <a class="pill" href="<?= href_page('admin/orders.php') ?>" style="<?= $statusFilter === '' ? 'background:#1f2937;color:#fff;' : '' ?>"><?= $lang === 'fr' ? 'Tous' : 'الكل' ?></a>
    <?php foreach ($validStatuses as $status): ?>
        <a class="pill" href="<?= href_page('admin/orders.php?status=' . $status) ?>" style="<?= $statusFilter === $status ? 'background:#1f2937;color:#fff;' : '' ?>"><?= e(get_order_status_label($status, $lang)) ?></a>
    <?php endforeach; ?>
</div>

<table>
    <thead>
        <tr>
            <th><?= $lang === 'fr' ? 'Commande' : 'الطلب' ?></th>
            <th><?= $lang === 'fr' ? 'Client' : 'العميل' ?></th>
            <th><?= $lang === 'fr' ? 'Ville' : 'المدينة' ?></th>
            <th><?= e(t('total')) ?></th>
            <th><?= $lang === 'fr' ? 'Statut' : 'الحالة' ?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td><strong><?= e($order['order_number']) ?></strong><br><small class="muted"><?= e($order['customer_phone']) ?></small></td>
                <td><?= e($order['customer_name']) ?></td>
                <td><?= e($order['city']) ?></td>
                <td><?= format_price((float) $order['total'], $lang) ?></td>
                <td><span class="badge" style="background:#fef3c7; color:#92400e;"><?= e(get_order_status_label($order['status'], $lang)) ?></span></td>
                <td>
                    <form action="<?= href_page('admin/orders.php') ?>" method="post" style="display:flex; gap:.4rem;">
                        <?= csrf_field() ?>
                        <input type="hidden" name="order_id" value="<?= (int) $order['id'] ?>">
                        <select name="status">
                            <?php foreach ($validStatuses as $status): ?>
                                <option value="<?= $status ?>" <?= $order['status'] === $status ? 'selected' : '' ?>><?= e(get_order_status_label($status, $lang)) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-outline"><?= $lang === 'fr' ? 'Mettre à jour' : 'تحديث' ?></button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($orders)): ?>
            <tr><td colspan="6" class="muted" style="text-align:center;"><?= $lang === 'fr' ? 'Aucune commande.' : 'لا توجد طلبات.' ?></td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
