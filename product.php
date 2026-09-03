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

$user = current_user();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    if (!$user) {
        redirect(href_page('account/login.php'));
    }
    if (($_POST['action'] ?? '') === 'wishlist') {
        $stmt = $pdo->prepare('INSERT IGNORE INTO wishlists (product_id, user_id) VALUES (?, ?)');
        $stmt->execute([$product['id'], $user['id']]);
        flash('success', $lang === 'fr' ? 'Produit ajouté aux favoris.' : 'تمت إضافة المنتج إلى المفضلة.');
    } elseif (($_POST['action'] ?? '') === 'review') {
        $rating = max(1, min(5, (int) ($_POST['rating'] ?? 5)));
        $comment = trim((string) ($_POST['comment'] ?? '')) ?: null;
        $stmt = $pdo->prepare('INSERT INTO reviews (product_id, user_id, rating, comment) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE rating = VALUES(rating), comment = VALUES(comment), is_approved = 0');
        $stmt->execute([$product['id'], $user['id'], $rating, $comment]);
        flash('success', $lang === 'fr' ? 'Merci. Votre avis sera vérifié avant publication.' : 'شكراً. سيتم مراجعة رأيك قبل نشره.');
    }
    redirect(href_page('product.php?slug=' . urlencode($slug)));
}

$relatedStmt = $pdo->prepare('SELECT * FROM products WHERE category_id = ? AND id != ? AND is_published = 1 LIMIT 3');
$relatedStmt->execute([$product['category_id'], $product['id']]);
$related = $relatedStmt->fetchAll();
$reviewStmt = $pdo->prepare('SELECT r.rating, r.comment, r.created_at, u.name FROM reviews r JOIN users u ON u.id = r.user_id WHERE r.product_id = ? AND r.is_approved = 1 ORDER BY r.created_at DESC');
$reviewStmt->execute([$product['id']]);
$reviews = $reviewStmt->fetchAll();

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
        <form action="<?= href_page('product.php?slug=' . urlencode($slug)) ?>" method="post" style="margin-top:1rem;">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="wishlist">
            <button class="btn btn-outline" type="submit"><?= $lang === 'fr' ? '♡ Ajouter aux favoris' : '♡ أضف إلى المفضلة' ?></button>
        </form>
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

<section style="margin-top:2.5rem;" class="panel">
    <h2><?= $lang === 'fr' ? 'Avis clients' : 'آراء العملاء' ?></h2>
    <?php foreach ($reviews as $review): ?>
        <div style="border-top:1px solid var(--border);padding:1rem 0;"><strong><?= e($review['name']) ?></strong> <span style="color:var(--brand);"><?= str_repeat('★', (int) $review['rating']) ?></span><p><?= e($review['comment'] ?? '') ?></p></div>
    <?php endforeach; ?>
    <?php if (!$reviews): ?><p class="muted"><?= $lang === 'fr' ? 'Aucun avis publié.' : 'لا توجد آراء منشورة.' ?></p><?php endif; ?>
    <?php if ($user): ?>
        <form action="<?= href_page('product.php?slug=' . urlencode($slug)) ?>" method="post" class="form-grid" style="margin-top:1rem;">
            <?= csrf_field() ?><input type="hidden" name="action" value="review">
            <select name="rating"><option value="5">★★★★★</option><option value="4">★★★★</option><option value="3">★★★</option><option value="2">★★</option><option value="1">★</option></select>
            <textarea name="comment" maxlength="1000" placeholder="<?= $lang === 'fr' ? 'Votre avis' : 'رأيك' ?>"></textarea>
            <button class="btn btn-dark" type="submit"><?= $lang === 'fr' ? 'Envoyer mon avis' : 'إرسال رأيي' ?></button>
        </form>
    <?php endif; ?>
</section>

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
