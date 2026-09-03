<?php
require __DIR__ . '/includes/bootstrap.php';

$categoryFilter = isset($_GET['category']) ? (int) $_GET['category'] : null;
$search = trim((string) ($_GET['q'] ?? ''));

$sql = 'SELECT * FROM products WHERE is_published = 1';
$params = [];

if ($categoryFilter) {
    $sql .= ' AND category_id = ?';
    $params[] = $categoryFilter;
}
if ($search !== '') {
    $sql .= ' AND (name_fr LIKE ? OR name_ar LIKE ? OR brand LIKE ?)';
    $like = "%{$search}%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}
$sql .= ' ORDER BY created_at DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$categories = $pdo->query('SELECT * FROM categories WHERE is_active = 1 ORDER BY sort_order')->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<div class="section-title">
    <div>
        <p class="muted" style="text-transform:uppercase; letter-spacing:.15em; font-size:.8rem;"><?= e(t('shop')) ?></p>
        <h1><?= $lang === 'fr' ? 'Boutique marocaine' : 'المتجر المغربي' ?></h1>
    </div>
</div>

<div style="display:flex; gap:.5rem; flex-wrap:wrap; margin-bottom:1.5rem;">
    <a class="pill" href="<?= href_page('products.php') ?>" style="<?= $categoryFilter ? '' : 'background:#1f2937;color:#fff;' ?>"><?= $lang === 'fr' ? 'Tous' : 'الكل' ?></a>
    <?php foreach ($categories as $category): ?>
        <a class="pill" href="<?= href_page('products.php?category=' . (int) $category['id']) ?>" style="<?= $categoryFilter === (int) $category['id'] ? 'background:#1f2937;color:#fff;' : '' ?>">
            <?= e($lang === 'fr' ? $category['name_fr'] : $category['name_ar']) ?>
        </a>
    <?php endforeach; ?>
</div>

<div class="grid grid-4">
    <?php foreach ($products as $product): ?>
        <?php include __DIR__ . '/includes/product_card.php'; ?>
    <?php endforeach; ?>
    <?php if (empty($products)): ?>
        <p class="muted"><?= $lang === 'fr' ? 'Aucun produit trouvé.' : 'لم يتم العثور على منتجات.' ?></p>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
