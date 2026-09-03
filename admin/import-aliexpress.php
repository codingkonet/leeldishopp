<?php
require __DIR__ . '/../includes/bootstrap.php';
require __DIR__ . '/../includes/product_import.php';
require __DIR__ . '/../includes/aliexpress_importer.php';
require_admin(href_page('account/login.php'));

$error = '';
$result = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    try {
        $url = trim((string) ($_POST['product_url'] ?? ''));
        $result = import_aliexpress_url($pdo, $url, false);
        flash('success', 'Produit importé en brouillon. Vérifiez le prix, le stock et les droits avant publication.');
    } catch (Throwable $exception) {
        $error = $exception->getMessage();
    }
}

require __DIR__ . '/includes/admin_header.php';
?>

<div style="display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:1rem;">
    <div>
        <p style="color:var(--brand);font-weight:700;font-size:.85rem;">ALIEXPRESS IMPORTER</p>
        <h1><?= $lang === 'fr' ? 'Importer avec une URL' : 'الاستيراد عبر الرابط' ?></h1>
        <p class="muted"><?= $lang === 'fr' ? 'Importez les informations publiques d’une fiche produit AliExpress.' : 'استورد المعلومات العامة من صفحة منتج علي إكسبريس.' ?></p>
    </div>
    <a class="btn btn-outline" href="<?= href_page('admin/import-products.php') ?>"><?= $lang === 'fr' ? 'Import CSV' : 'استيراد CSV' ?></a>
</div>

<div class="panel" style="margin-top:1.5rem;max-width:760px;">
    <h2><?= $lang === 'fr' ? 'URL de la fiche produit' : 'رابط صفحة المنتج' ?></h2>
    <p class="muted"><?= $lang === 'fr' ? 'Le produit sera créé non publié dans la catégorie « AliExpress imports ».' : 'سيتم إنشاء المنتج بدون نشر في فئة استيرادات علي إكسبريس.' ?></p>
    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
    <?php if ($result): ?><div class="alert alert-success">« <?= e($result['name']) ?> » importé. <a href="<?= href_page('admin/products.php') ?>">Vérifier le produit</a></div><?php endif; ?>
    <form method="post" class="form-grid" style="margin-top:1rem;">
        <?= csrf_field() ?>
        <input type="url" name="product_url" required placeholder="https://www.aliexpress.com/item/..." value="<?= e($_POST['product_url'] ?? '') ?>">
        <button class="btn btn-brand" type="submit">↓ <?= $lang === 'fr' ? 'Importer en brouillon' : 'استيراد كمسودة' ?></button>
    </form>
</div>

<div class="panel" style="margin-top:1.5rem;background:#fffaf0;max-width:760px;">
    <strong><?= $lang === 'fr' ? 'Avant de publier' : 'قبل النشر' ?></strong>
    <p class="muted" style="margin-bottom:0;"><?= $lang === 'fr' ? 'AliExpress peut bloquer les requêtes automatisées ou ne pas exposer ses données. Dans ce cas, utilisez un export CSV ou l’API officielle. Vérifiez toujours les autorisations, la TVA, les frais d’importation, le stock, les images et les descriptions.' : 'قد يمنع علي إكسبريس الطلبات الآلية أو لا يعرض البيانات. استخدم CSV أو API الرسمية عند الحاجة. تحقق من الأذونات والضرائب وتكاليف الاستيراد والمخزون والصور والأوصاف.' ?></p>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
