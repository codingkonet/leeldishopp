<?php
require __DIR__ . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    $productId = (int) ($_POST['product_id'] ?? 0);

    if ($action === 'add' && $productId > 0) {
        $quantity = max(1, (int) ($_POST['quantity'] ?? 1));
        cart_add($productId, $quantity);
    } elseif ($action === 'update' && $productId > 0) {
        cart_update($productId, (int) ($_POST['quantity'] ?? 1));
    } elseif ($action === 'remove' && $productId > 0) {
        cart_remove($productId);
    } elseif ($action === 'clear') {
        cart_clear();
    }

    redirect(href_page('cart.php'));
}

$lines = cart_lines($pdo);
$subtotal = cart_subtotal($pdo);
$digitalOnly = $lines !== [] && count(array_filter($lines, static fn (array $line): bool => $line['product']['product_type'] !== 'DIGITAL')) === 0;
$shippingFee = ($subtotal === 0.0 || $subtotal >= FREE_SHIPPING_THRESHOLD || $digitalOnly) ? 0.0 : DEFAULT_SHIPPING_FEES['STANDARD'];
$total = $subtotal + $shippingFee;

require __DIR__ . '/includes/header.php';
?>

<h1><?= e(t('cart')) ?></h1>

<?php if (empty($lines)): ?>
    <div class="panel" style="text-align:center; margin-top:2rem;">
        <p><?= $lang === 'fr' ? 'Votre panier est vide.' : 'سلتك فارغة.' ?></p>
        <a href="<?= href_page('products.php') ?>" class="btn btn-brand"><?= $lang === 'fr' ? 'Voir les produits' : 'تصفح المنتجات' ?></a>
    </div>
<?php else: ?>
    <div class="grid" style="grid-template-columns: 1.5fr .7fr; margin-top:1.5rem;">
        <div style="display:flex; flex-direction:column; gap:1rem;">
            <?php foreach ($lines as $line): $p = $line['product']; ?>
                <div class="panel" style="display:flex; justify-content:space-between; align-items:center;">
                    <div>
                        <h3><?= e($lang === 'fr' ? $p['name_fr'] : $p['name_ar']) ?></h3>
                        <p class="muted"><?= format_price((float) $p['price'], $lang) ?> / unitaire</p>
                    </div>
                    <div style="display:flex; align-items:center; gap:1rem;">
                        <form action="<?= href_page('cart.php') ?>" method="post" style="display:flex; align-items:center; gap:.4rem;">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="product_id" value="<?= (int) $p['id'] ?>">
                            <input type="number" name="quantity" value="<?= (int) $line['quantity'] ?>" min="1" max="<?= (int) $p['stock'] ?>" style="width:70px;">
                            <button type="submit" class="btn btn-outline">↻</button>
                        </form>
                        <form action="<?= href_page('cart.php') ?>" method="post">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="remove">
                            <input type="hidden" name="product_id" value="<?= (int) $p['id'] ?>">
                            <button type="submit" class="btn btn-outline" style="color:#b91c1c; border-color:#fecaca;">✕</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <aside class="panel">
            <h2><?= $lang === 'fr' ? 'Résumé' : 'ملخص' ?></h2>
            <p style="display:flex; justify-content:space-between;"><span>Sous-total</span><span><?= format_price($subtotal, $lang) ?></span></p>
            <p style="display:flex; justify-content:space-between;"><span><?= $lang === 'fr' ? 'Livraison' : 'التوصيل' ?></span><span><?= format_price($shippingFee, $lang) ?></span></p>
            <p style="display:flex; justify-content:space-between; font-weight:800; border-top:1px solid var(--border); padding-top:.6rem;"><span><?= e(t('total')) ?></span><span><?= format_price($total, $lang) ?></span></p>
            <a href="<?= href_page('checkout.php') ?>" class="btn btn-dark" style="width:100%; justify-content:center; margin-top:1rem;"><?= $lang === 'fr' ? 'Passer commande' : 'إتمام الطلب' ?></a>
            <a href="<?= href_page('products.php') ?>" class="btn btn-outline" style="width:100%; justify-content:center; margin-top:.6rem;"><?= e(t('continueShopping')) ?></a>
        </aside>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
