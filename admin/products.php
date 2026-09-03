<?php
require __DIR__ . '/../includes/bootstrap.php';
require_admin(href_page('account/login.php'));

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    verify_csrf();
    $productId = (int) ($_POST['product_id'] ?? 0);
    if ($productId > 0) {
        $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
        $stmt->execute([$productId]);
        flash('success', $lang === 'fr' ? 'Produit supprimé.' : 'تم حذف المنتج.');
    }
    redirect(href_page('admin/products.php'));
}

$products = $pdo->query('SELECT p.*, c.name_fr AS category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id ORDER BY p.created_at DESC')->fetchAll();

require __DIR__ . '/includes/admin_header.php';
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
    <h1><?= $lang === 'fr' ? 'Produits' : 'المنتجات' ?></h1>
    <a href="<?= href_page('admin/product-form.php') ?>" class="btn btn-brand">+ <?= $lang === 'fr' ? 'Ajouter un produit' : 'إضافة منتج' ?></a>
</div>

<table>
    <thead>
        <tr>
            <th>SKU</th>
            <th><?= $lang === 'fr' ? 'Nom' : 'الاسم' ?></th>
            <th><?= $lang === 'fr' ? 'Catégorie' : 'الفئة' ?></th>
            <th><?= $lang === 'fr' ? 'Prix' : 'السعر' ?></th>
            <th><?= $lang === 'fr' ? 'Stock' : 'المخزون' ?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $product): ?>
            <tr>
                <td><?= e($product['sku']) ?></td>
                <td><?= e($product['name_fr']) ?></td>
                <td><?= e($product['category_name'] ?? '—') ?></td>
                <td><?= format_price((float) $product['price'], $lang) ?></td>
                <td><?= (int) $product['stock'] ?></td>
                <td style="display:flex; gap:.5rem;">
                    <a href="<?= href_page('admin/product-form.php?id=' . (int) $product['id']) ?>" class="btn btn-outline"><?= $lang === 'fr' ? 'Modifier' : 'تعديل' ?></a>
                    <form action="<?= href_page('admin/products.php') ?>" method="post" onsubmit="return confirm('<?= $lang === 'fr' ? 'Confirmer la suppression ?' : 'تأكيد الحذف؟' ?>');">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                        <button type="submit" class="btn btn-outline" style="color:#b91c1c; border-color:#fecaca;"><?= $lang === 'fr' ? 'Supprimer' : 'حذف' ?></button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($products)): ?>
            <tr><td colspan="6" class="muted" style="text-align:center;"><?= $lang === 'fr' ? 'Aucun produit.' : 'لا توجد منتجات.' ?></td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
