<?php
require __DIR__ . '/../includes/bootstrap.php';
require __DIR__ . '/../includes/product_import.php';
require_admin(href_page('account/login.php'));

if (isset($_GET['template'])) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="lebeldishop-products-template.csv"');
    $output = fopen('php://output', 'wb');
    fputcsv($output, ['sku', 'name_fr', 'name_ar', 'description_fr', 'description_ar', 'price', 'old_price', 'stock', 'brand', 'image_url', 'category', 'source_name', 'source_url']);
    fputcsv($output, ['ALI-001', 'Exemple produit', 'منتج تجريبي', 'Description produit', 'وصف المنتج', '100', '150', '10', 'Supplier brand', 'https://example.com/image.jpg', 'Accessoires', 'AliExpress', 'https://example.com/product']);
    fclose($output);
    exit;
}

$error = '';
$result = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    try {
        $source = trim((string) ($_POST['source_name'] ?? ''));
        if (!in_array($source, ['AliExpress', 'Alibaba', 'Site marocain', 'Autre fournisseur'], true)) {
            throw new RuntimeException('Choose a valid source.');
        }
        $upload = $_FILES['products_csv'] ?? [];
        if (($upload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Please upload a CSV file.');
        }
        if (($upload['size'] ?? 0) > 5 * 1024 * 1024 || strtolower(pathinfo((string) $upload['name'], PATHINFO_EXTENSION)) !== 'csv') {
            throw new RuntimeException('The CSV file must be 5 MB or smaller.');
        }
        $result = import_products_csv($pdo, (string) $upload['tmp_name'], $source, isset($_POST['publish']));
    } catch (Throwable $exception) {
        $error = $exception->getMessage();
    }
}

require __DIR__ . '/includes/admin_header.php';
?>

<div style="display:flex; justify-content:space-between; align-items:flex-end; flex-wrap:wrap; gap:1rem;">
    <div>
        <p style="color:var(--brand); font-weight:700; font-size:.85rem;">PRODUCT IMPORT</p>
        <h1><?= $lang === 'fr' ? 'Importer des produits' : 'استيراد المنتجات' ?></h1>
        <p class="muted"><?= $lang === 'fr' ? 'AliExpress, Alibaba et fournisseurs marocains' : 'علي إكسبريس وعلي بابا والموردون المغاربة' ?></p>
    </div>
    <a class="btn btn-outline" href="<?= href_page('admin/import-products.php?template=1') ?>">↓ <?= $lang === 'fr' ? 'Télécharger le modèle CSV' : 'تحميل نموذج CSV' ?></a>
</div>

<div class="panel" style="margin-top:1.5rem;">
    <h2><?= $lang === 'fr' ? 'Import CSV fournisseur' : 'استيراد CSV من المورد' ?></h2>
    <p class="muted"><?= $lang === 'fr' ? 'Exportez votre catalogue depuis le tableau de bord officiel du fournisseur, adaptez les colonnes au modèle, puis importez-le ici.' : 'صدّر الكتالوج من لوحة المورد الرسمية، ثم عدّل الأعمدة حسب النموذج واستورده هنا.' ?></p>
    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
    <?php if ($result): ?>
        <div class="alert alert-success">
            <?= (int) $result['created'] ?> <?= $lang === 'fr' ? 'créés' : 'تم إنشاؤها' ?>,
            <?= (int) $result['updated'] ?> <?= $lang === 'fr' ? 'mis à jour' : 'تم تحديثها' ?>,
            <?= (int) $result['skipped'] ?> <?= $lang === 'fr' ? 'ignorés' : 'تم تجاهلها' ?>.
            <?php if ($result['errors']): ?><details><summary>Errors</summary><ul><?php foreach ($result['errors'] as $importError): ?><li><?= e($importError) ?></li><?php endforeach; ?></ul></details><?php endif; ?>
        </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="form-grid cols-2" style="margin-top:1rem;">
        <?= csrf_field() ?>
        <select name="source_name" required>
            <option value=""><?= $lang === 'fr' ? 'Choisir la source' : 'اختر المصدر' ?></option>
            <option>AliExpress</option>
            <option>Alibaba</option>
            <option>Site marocain</option>
            <option>Autre fournisseur</option>
        </select>
        <input type="file" name="products_csv" accept=".csv,text/csv" required>
        <label style="grid-column:span 2;"><input type="checkbox" name="publish"> <?= $lang === 'fr' ? 'Publier immédiatement les produits importés' : 'نشر المنتجات المستوردة مباشرة' ?></label>
        <button type="submit" class="btn btn-brand" style="grid-column:span 2;">↑ <?= $lang === 'fr' ? 'Importer les produits' : 'استيراد المنتجات' ?></button>
    </form>
</div>

<div class="panel" style="margin-top:1.5rem; background:#fffaf0;">
    <strong><?= $lang === 'fr' ? 'Important' : 'مهم' ?></strong>
    <p class="muted" style="margin-bottom:0;"><?= $lang === 'fr' ? 'Utilisez les API officielles ou les exports autorisés par chaque fournisseur. Ne contournez pas les protections anti-bot et vérifiez les droits sur les images, descriptions, marques et prix avant publication.' : 'استخدم واجهات API الرسمية أو الملفات المسموح بها من كل مورد. لا تتجاوز حماية المواقع وتحقق من حقوق الصور والأوصاف والعلامات والأسعار قبل النشر.' ?></p>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
