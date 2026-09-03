<?php
require __DIR__ . '/../includes/bootstrap.php';
require_admin(href_page('account/login.php'));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $reviewId = (int) ($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';
    if ($reviewId > 0 && in_array($action, ['approve', 'reject'], true)) {
        $stmt = $pdo->prepare('UPDATE reviews SET is_approved = ? WHERE id = ?');
        $stmt->execute([$action === 'approve' ? 1 : 0, $reviewId]);
        flash('success', $action === 'approve' ? 'Avis approuvé.' : 'Avis masqué.');
    }
    redirect(href_page('admin/reviews.php'));
}

$reviews = $pdo->query('SELECT r.*, p.name_fr AS product_name, u.name AS customer_name FROM reviews r JOIN products p ON p.id = r.product_id JOIN users u ON u.id = r.user_id ORDER BY r.created_at DESC')->fetchAll();
require __DIR__ . '/includes/admin_header.php';
?>
<h1><?= $lang === 'fr' ? 'Avis clients' : 'آراء العملاء' ?></h1>
<table style="margin-top:1rem;"><thead><tr><th>Produit</th><th>Client</th><th>Note</th><th>Avis</th><th>Statut</th><th></th></tr></thead><tbody>
<?php foreach ($reviews as $review): ?><tr><td><?= e($review['product_name']) ?></td><td><?= e($review['customer_name']) ?></td><td><?= str_repeat('★', (int) $review['rating']) ?></td><td><?= e($review['comment'] ?? '') ?></td><td><?= $review['is_approved'] ? 'Publié' : 'En attente' ?></td><td style="display:flex;gap:.4rem;"><form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $review['id'] ?>"><input type="hidden" name="action" value="approve"><button class="btn btn-outline" type="submit">Approuver</button></form><form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $review['id'] ?>"><input type="hidden" name="action" value="reject"><button class="btn btn-outline" type="submit">Masquer</button></form></td></tr><?php endforeach; ?>
<?php if (!$reviews): ?><tr><td colspan="6" class="muted" style="text-align:center;">Aucun avis.</td></tr><?php endif; ?>
</tbody></table>
<?php require __DIR__ . '/includes/admin_footer.php'; ?>
