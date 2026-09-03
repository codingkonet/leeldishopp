<?php
require __DIR__ . '/includes/bootstrap.php';

$featured = $pdo->query('SELECT * FROM products WHERE is_published = 1 ORDER BY is_featured DESC, created_at DESC LIMIT 4')->fetchAll();
$categories = $pdo->query('SELECT * FROM categories WHERE is_active = 1 ORDER BY sort_order LIMIT 4')->fetchAll();
$services = $pdo->query('SELECT * FROM services WHERE available = 1 ORDER BY created_at DESC LIMIT 2')->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <span class="badge" style="background: rgba(255,255,255,.15); color:#fff;"><?= $lang === 'fr' ? 'Découverte du Maroc' : 'اكتشف المغرب' ?></span>
    <h1><?= $lang === 'fr' ? 'Découvrez le meilleur du Maroc' : 'اكتشف أفضل المنتجات والخدمات المغربية' ?></h1>
    <p><?= $lang === 'fr'
        ? 'Des produits authentiques, des services de confiance et des créations marocaines à la pointe du style.'
        : 'منتجات أصلية وخدمات موثوقة وإبداعات مغربية تجمع بين الأصالة والحداثة.' ?></p>
    <div style="display:flex; gap:1rem; margin-top:1rem;">
        <a href="<?= href_page('products.php') ?>" class="btn btn-brand"><?= e(t('buyNow')) ?></a>
        <a href="<?= href_page('services.php') ?>" class="btn btn-outline" style="background:rgba(255,255,255,.08); border-color:rgba(255,255,255,.3); color:#fff;"><?= e(t('services')) ?></a>
    </div>
</section>

<section>
    <div class="section-title">
        <h2><?= e(t('popularProducts')) ?></h2>
        <a href="<?= href_page('products.php') ?>"><?= e(t('viewDetails')) ?></a>
    </div>
    <div class="grid grid-4">
        <?php foreach ($featured as $product): ?>
            <?php include __DIR__ . '/includes/product_card.php'; ?>
        <?php endforeach; ?>
        <?php if (empty($featured)): ?>
            <p class="muted"><?= $lang === 'fr' ? 'Aucun produit pour le moment.' : 'لا توجد منتجات حالياً.' ?></p>
        <?php endif; ?>
    </div>
</section>

<section style="margin-top:2.5rem;">
    <div class="section-title"><h2><?= e(t('categories')) ?></h2></div>
    <div class="grid grid-4">
        <?php foreach ($categories as $category): ?>
            <div class="card">
                <img src="<?= e($category['image']) ?>" alt="<?= e($category['name_fr']) ?>">
                <div class="card-body">
                    <h3><?= e($lang === 'fr' ? $category['name_fr'] : $category['name_ar']) ?></h3>
                    <p class="muted"><?= e($lang === 'fr' ? $category['description_fr'] : $category['description_ar']) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section style="margin-top:2.5rem;">
    <div class="section-title">
        <h2><?= e(t('servicesPopular')) ?></h2>
        <a href="<?= href_page('services.php') ?>"><?= e(t('viewDetails')) ?></a>
    </div>
    <div class="grid grid-2">
        <?php foreach ($services as $service): ?>
            <div class="panel">
                <div style="display:flex; justify-content:space-between; gap:1rem;">
                    <h3><?= e($lang === 'fr' ? $service['title_fr'] : $service['title_ar']) ?></h3>
                    <span class="badge" style="background:#fdf4d8; color:#8a5a12;"><?= format_price((float) $service['price'], $lang) ?></span>
                </div>
                <p class="muted"><?= e($lang === 'fr' ? $service['description_fr'] : $service['description_ar']) ?></p>
                <p class="muted"><?= e($service['location'] ?? '') ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
