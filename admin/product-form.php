<?php
require __DIR__ . '/../includes/bootstrap.php';
require_admin(href_page('account/login.php'));

$productId = isset($_GET['id']) ? (int) $_GET['id'] : null;
$product = null;
$error = '';

if ($productId) {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$productId]);
    $product = $stmt->fetch();
    if (!$product) {
        redirect(href_page('admin/products.php'));
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $sku = trim((string) ($_POST['sku'] ?? ''));
    $slug = trim((string) ($_POST['slug'] ?? ''));
    $nameFr = trim((string) ($_POST['name_fr'] ?? ''));
    $nameAr = trim((string) ($_POST['name_ar'] ?? ''));
    $descriptionFr = trim((string) ($_POST['description_fr'] ?? ''));
    $descriptionAr = trim((string) ($_POST['description_ar'] ?? ''));
    $price = (float) ($_POST['price'] ?? 0);
    $oldPrice = $_POST['old_price'] !== '' ? (float) $_POST['old_price'] : null;
    $stock = (int) ($_POST['stock'] ?? 0);
    $categoryId = (int) ($_POST['category_id'] ?? 0);
    $image = trim((string) ($_POST['image'] ?? ''));
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
    $isPublished = isset($_POST['is_published']) ? 1 : 0;

    try {
        $uploadedImage = upload_brand_asset($_FILES['image_file'] ?? [], 'product');
        $image = $uploadedImage ?: $image;
    } catch (Throwable $exception) {
        $error = $exception->getMessage();
    }

    if ($error === '' && ($sku === '' || $slug === '' || $nameFr === '' || $nameAr === '' || $price <= 0 || $categoryId <= 0 || $image === '')) {
        $error = $lang === 'fr' ? 'Merci de remplir tous les champs obligatoires.' : 'يرجى ملء جميع الحقول المطلوبة.';
    } elseif ($error === '') {
        if ($product) {
            $stmt = $pdo->prepare('UPDATE products SET sku=?, slug=?, name_fr=?, name_ar=?, description_fr=?, description_ar=?, price=?, old_price=?, stock=?, category_id=?, image=?, is_featured=?, is_published=? WHERE id=?');
            $stmt->execute([$sku, $slug, $nameFr, $nameAr, $descriptionFr, $descriptionAr, $price, $oldPrice, $stock, $categoryId, $image, $isFeatured, $isPublished, $product['id']]);
            flash('success', $lang === 'fr' ? 'Produit mis à jour.' : 'تم تحديث المنتج.');
        } else {
            $stmt = $pdo->prepare('INSERT INTO products (sku, slug, name_fr, name_ar, description_fr, description_ar, price, old_price, stock, category_id, image, is_featured, is_published) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)');
            $stmt->execute([$sku, $slug, $nameFr, $nameAr, $descriptionFr, $descriptionAr, $price, $oldPrice, $stock, $categoryId, $image, $isFeatured, $isPublished]);
            flash('success', $lang === 'fr' ? 'Produit créé.' : 'تم إنشاء المنتج.');
        }
        redirect(href_page('admin/products.php'));
    }
}

$categories = $pdo->query('SELECT * FROM categories ORDER BY sort_order')->fetchAll();

require __DIR__ . '/includes/admin_header.php';
?>

<h1><?= $product ? ($lang === 'fr' ? 'Modifier le produit' : 'تعديل المنتج') : ($lang === 'fr' ? 'Ajouter un produit' : 'إضافة منتج') ?></h1>

<?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

<form action="<?= href_page('admin/product-form.php' . ($productId ? '?id=' . $productId : '')) ?>" method="post" enctype="multipart/form-data" class="panel form-grid cols-2" style="margin-top:1rem;">
    <?= csrf_field() ?>
    <input type="text" name="sku" required placeholder="SKU" value="<?= e($product['sku'] ?? '') ?>">
    <input type="text" name="slug" required placeholder="Slug" value="<?= e($product['slug'] ?? '') ?>">
    <input type="text" name="name_fr" required placeholder="<?= $lang === 'fr' ? 'Nom (français)' : 'الاسم (فرنسي)' ?>" value="<?= e($product['name_fr'] ?? '') ?>">
    <input type="text" name="name_ar" required placeholder="<?= $lang === 'fr' ? 'Nom (arabe)' : 'الاسم (عربي)' ?>" value="<?= e($product['name_ar'] ?? '') ?>">
    <textarea name="description_fr" placeholder="<?= $lang === 'fr' ? 'Description (français)' : 'الوصف (فرنسي)' ?>" style="grid-column: span 2;"><?= e($product['description_fr'] ?? '') ?></textarea>
    <textarea name="description_ar" placeholder="<?= $lang === 'fr' ? 'Description (arabe)' : 'الوصف (عربي)' ?>" style="grid-column: span 2;"><?= e($product['description_ar'] ?? '') ?></textarea>
    <input type="number" step="0.01" name="price" required placeholder="<?= $lang === 'fr' ? 'Prix' : 'السعر' ?>" value="<?= e((string) ($product['price'] ?? '')) ?>">
    <input type="number" step="0.01" name="old_price" placeholder="<?= $lang === 'fr' ? 'Ancien prix (optionnel)' : 'السعر القديم (اختياري)' ?>" value="<?= e((string) ($product['old_price'] ?? '')) ?>">
    <input type="number" name="stock" required placeholder="<?= $lang === 'fr' ? 'Stock' : 'المخزون' ?>" value="<?= e((string) ($product['stock'] ?? 0)) ?>">
    <select name="category_id" required>
        <option value=""><?= $lang === 'fr' ? 'Choisir une catégorie' : 'اختر فئة' ?></option>
        <?php foreach ($categories as $category): ?>
            <option value="<?= (int) $category['id'] ?>" <?= (int) ($product['category_id'] ?? 0) === (int) $category['id'] ? 'selected' : '' ?>><?= e($category['name_fr']) ?></option>
        <?php endforeach; ?>
    </select>
    <input type="text" name="image" placeholder="<?= $lang === 'fr' ? 'URL image (ou téléversez ci-dessous)' : 'رابط الصورة (أو ارفع صورة أدناه)' ?>" style="grid-column: span 2;" value="<?= e($product['image'] ?? '') ?>">
    <label style="grid-column: span 2;">Ou téléverser une image <input type="file" name="image_file" accept="image/png,image/jpeg,image/webp"></label>
    <label><input type="checkbox" name="is_featured" <?= !empty($product['is_featured']) ? 'checked' : '' ?>> <?= $lang === 'fr' ? 'Mis en avant' : 'مميز' ?></label>
    <label><input type="checkbox" name="is_published" <?= $product === null || !empty($product['is_published']) ? 'checked' : '' ?>> <?= $lang === 'fr' ? 'Publié' : 'منشور' ?></label>
    <button type="submit" class="btn btn-brand" style="grid-column: span 2;"><?= $lang === 'fr' ? 'Enregistrer' : 'حفظ' ?></button>
</form>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
