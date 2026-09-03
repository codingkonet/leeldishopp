<?php
require __DIR__ . '/includes/bootstrap.php';

$slug = trim((string) ($_GET['slug'] ?? ''));
$stmt = $pdo->prepare('SELECT * FROM products WHERE slug = ? AND is_published = 1 LIMIT 1');
$stmt->execute([$slug]);
$product = $stmt->fetch();

if (!$product) {
    http_response_code(404);
    require __DIR__ . '/includes/header.php';
    echo '<p>' . ($lang === 'fr' ? 'Produit introuvable.' : 'المنتج غير موجود.') . '</p>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$relatedStmt = $pdo->prepare('SELECT * FROM products WHERE category_id = ? AND id != ? AND is_published = 1 LIMIT 3');
$relatedStmt->execute([$product['category_id'], $product['id']]);
$related = $relatedStmt->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<p class="muted">
    <a href="<?= href_page('index.php') ?>"><?= e(t('home')) ?></a> /
    <a href="<?= href_page('products.php') ?>"><?= e(t('shop')) ?></a>
</p>

<div class="grid grid-2" style="grid-template-columns: 1.2fr .8fr;">
    <div>
        <img src="<?= e($product['image']) ?>" alt="<?= e($product['name_fr']) ?>" style="width:100%; border-radius:1.5rem; max-height:500px; object-fit:cover;">
    </div>
    <div class="panel">
        <h1><?= e($lang === 'fr' ? $product['name_fr'] : $product['name_ar']) ?></h1>
        <div style="margin:1rem 0;">
            <span class="price"><?= format_price((float) $product['price'], $lang) ?></span>
            <?php if ($product['old_price']): ?>
                <span class="price-old"><?= format_price((float) $product['old_price'], $lang) ?></span>
            <?php endif; ?>
        </div>
        <p><?= e($lang === 'fr' ? $product['description_fr'] : $product['description_ar']) ?></p>

        <form action="<?= href_page('cart.php') ?>" method="post" class="form-grid cols-2">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
            <input type="number" name="quantity" value="1" min="1" max="<?= (int) $product['stock'] ?>">
            <button type="submit" class="btn btn-dark" <?= (int) $product['stock'] < 1 ? 'disabled' : '' ?>><?= e(t('addToCart')) ?></button>
        </form>

        <div class="panel" style="margin-top:1rem; background:#f8fafc;">
            <p style="display:flex; justify-content:space-between;"><span><?= $lang === 'fr' ? 'Disponibilité' : 'التوفر' ?></span><strong><?= (int) $product['stock'] > 0 ? ($lang === 'fr' ? 'En stock' : 'متوفر') : ($lang === 'fr' ? 'Rupture' : 'غير متوفر') ?></strong></p>
            <p style="display:flex; justify-content:space-between;"><span><?= $lang === 'fr' ? 'Vendeur' : 'البائع' ?></span><strong><?= e(APP_NAME) ?></strong></p>
        </div>
    </div>
</div>

<?php if ($related): ?>
<section style="margin-top:2.5rem;">
    <h2><?= $lang === 'fr' ? 'Produits similaires' : 'منتجات مشابهة' ?></h2>
    <div class="grid grid-4">
        <?php foreach ($related as $product): ?>
            <?php include __DIR__ . '/includes/product_card.php'; ?>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
