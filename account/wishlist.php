<?php
require __DIR__ . '/../includes/bootstrap.php';
require_login(href_page('account/login.php'));
$user = current_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $productId = (int) ($_POST['product_id'] ?? 0);
    $stmt = $pdo->prepare('DELETE FROM wishlists WHERE user_id = ? AND product_id = ?');
    $stmt->execute([$user['id'], $productId]);
    flash('success', $lang === 'fr' ? 'Produit retiré des favoris.' : 'تم حذف المنتج من المفضلة.');
    redirect(href_page('account/wishlist.php'));
}

$stmt = $pdo->prepare('SELECT p.* FROM wishlists w JOIN products p ON p.id = w.product_id WHERE w.user_id = ? ORDER BY w.created_at DESC');
$stmt->execute([$user['id']]);
$products = $stmt->fetchAll();

require __DIR__ . '/../includes/header.php';
?>
<h1><?= $lang === 'fr' ? 'Mes favoris' : 'المفضلة' ?></h1>
<div class="grid grid-4" style="margin-top:1.5rem;">
<?php foreach ($products as $product): ?>
    <?php include __DIR__ . '/../includes/product_card.php'; ?>
    <form method="post" style="margin-top:-1rem;">
        <?= csrf_field() ?><input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
        <button class="btn btn-outline" type="submit"><?= $lang === 'fr' ? 'Retirer' : 'إزالة' ?></button>
    </form>
<?php endforeach; ?>
</div>
<?php if (!$products): ?><p class="panel muted" style="margin-top:1.5rem;text-align:center;"><?= $lang === 'fr' ? 'Aucun favori.' : 'لا توجد مفضلة.' ?></p><?php endif; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>
