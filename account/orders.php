<?php
require __DIR__ . '/../includes/bootstrap.php';

require_login(href_page('account/login.php'));
$user = current_user();

$stmt = $pdo->prepare('SELECT id, order_number, total, status, created_at FROM orders WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([$user['id']]);
$orders = $stmt->fetchAll();

require __DIR__ . '/../includes/header.php';
?>

<a href="<?= href_page('account/index.php') ?>" style="color:var(--brand); font-weight:700;">&larr; <?= $lang === 'fr' ? 'Retour au compte' : 'العودة للحساب' ?></a>
<h1 style="margin-top:.6rem;"><?= $lang === 'fr' ? 'Mes commandes' : 'طلباتي' ?></h1>

<?php if (empty($orders)): ?>
    <p class="panel muted" style="text-align:center;"><?= $lang === 'fr' ? 'Vous n’avez pas encore de commande.' : 'ليس لديك أي طلب بعد.' ?></p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th><?= $lang === 'fr' ? 'Commande' : 'الطلب' ?></th>
                <th><?= $lang === 'fr' ? 'Date' : 'التاريخ' ?></th>
                <th><?= $lang === 'fr' ? 'Statut' : 'الحالة' ?></th>
                <th><?= e(t('total')) ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><strong><?= e($order['order_number']) ?></strong></td>
                    <td><?= e(date('d/m/Y', strtotime($order['created_at']))) ?></td>
                    <td><span class="badge" style="background:#fdf4d8; color:#8a5a12;"><?= e(get_order_status_label($order['status'], $lang)) ?></span></td>
                    <td><?= format_price((float) $order['total'], $lang) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require __DIR__ . '/../includes/footer.php'; ?>
