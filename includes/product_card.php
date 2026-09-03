<?php
/** @var array $product expected in scope from including page */
$discount = null;
if (!empty($product['old_price']) && (float) $product['old_price'] > (float) $product['price']) {
    $discount = (int) round((1 - ((float) $product['price'] / (float) $product['old_price'])) * 100);
}
?>
<div class="card">
    <div style="position:relative;">
        <img src="<?= e($product['image']) ?>" alt="<?= e($product['name_fr']) ?>">
        <?php if ($discount): ?>
            <span class="badge badge-sale" style="position:absolute; top:.6rem; left:.6rem;">-<?= $discount ?>%</span>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <h3><?= e($lang === 'fr' ? $product['name_fr'] : $product['name_ar']) ?></h3>
        <div>
            <span class="price"><?= format_price((float) $product['price'], $lang) ?></span>
            <?php if ($product['old_price']): ?>
                <span class="price-old"><?= format_price((float) $product['old_price'], $lang) ?></span>
            <?php endif; ?>
        </div>
        <p>
            <span class="badge badge-stock"><?= (int) $product['stock'] > 0 ? ($lang === 'fr' ? 'En stock' : 'متوفر') : ($lang === 'fr' ? 'Rupture' : 'غير متوفر') ?></span>
        </p>
        <div style="display:flex; gap:.5rem;">
            <form action="<?= href_page('cart.php') ?>" method="post" style="flex:1;">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                <button type="submit" class="btn btn-dark" style="width:100%;" <?= (int) $product['stock'] < 1 ? 'disabled' : '' ?>><?= e(t('addToCart')) ?></button>
            </form>
            <a href="<?= href_page('product.php?slug=' . urlencode($product['slug'])) ?>" class="btn btn-outline"><?= e(t('viewDetails')) ?></a>
        </div>
    </div>
</div>
