<?php
require __DIR__ . '/../includes/bootstrap.php';
require __DIR__ . '/../includes/extensions.php';
require_admin(href_page('account/login.php'));

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    try {
        $action = $_POST['action'] ?? '';
        $type = $_POST['type'] ?? '';
        if ($action === 'import') {
            $slug = import_extension($type, $_FILES['extension'] ?? []);
            flash('success', ($type === 'plugin' ? 'Plugin' : 'Thème') . " '{$slug}' importé avec succès.");
        } elseif ($action === 'activate') {
            activate_extension($type, (string) ($_POST['slug'] ?? ''));
            flash('success', ($type === 'plugin' ? 'Plugin' : 'Thème') . ' activé.');
        }
    } catch (Throwable $exception) {
        flash('error', $exception->getMessage());
    }
    redirect(href_page('admin/extensions.php'));
}

$plugins = extension_files('plugin');
$themes = extension_files('theme');
$activePlugin = active_extension('plugin');
$activeTheme = active_extension('theme');

require __DIR__ . '/includes/admin_header.php';
?>

<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem;">
    <div>
        <p style="color:var(--brand); font-weight:700; font-size:.85rem;">EXTENSIONS</p>
        <h1><?= $lang === 'fr' ? 'Plugins et thèmes' : 'الإضافات والقوالب' ?></h1>
        <p class="muted"><?= $lang === 'fr' ? 'Importez uniquement des ZIP provenant d’une source de confiance.' : 'استورد ملفات ZIP من مصادر موثوقة فقط.' ?></p>
    </div>
</div>

<div class="grid grid-2" style="margin-top:1.5rem;">
    <?php foreach ([['plugin', $plugins, $activePlugin, 'Plugins', 'الإضافات'], ['theme', $themes, $activeTheme, 'Thèmes', 'القوالب']] as [$type, $items, $active, $titleFr, $titleAr]): ?>
        <section class="panel">
            <h2><?= $lang === 'fr' ? $titleFr : $titleAr ?></h2>
            <form action="<?= href_page('admin/extensions.php') ?>" method="post" enctype="multipart/form-data" style="margin-top:1rem;">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="import">
                <input type="hidden" name="type" value="<?= e($type) ?>">
                <input type="file" name="extension" accept=".zip,application/zip" required>
                <button type="submit" class="btn btn-brand" style="margin-top:.6rem;">+ <?= $lang === 'fr' ? 'Importer un ZIP' : 'استيراد ZIP' ?></button>
            </form>

            <div style="margin-top:1.5rem;">
                <?php if (empty($items)): ?>
                    <p class="muted"><?= $lang === 'fr' ? 'Aucun élément importé.' : 'لا توجد إضافات مستوردة.' ?></p>
                <?php else: ?>
                    <?php foreach ($items as $item): ?>
                        <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid var(--border); padding:.8rem 0; gap:1rem;">
                            <div>
                                <strong><?= e($item['slug']) ?></strong>
                                <?php if ($active === $item['slug']): ?><span class="badge badge-stock" style="margin-left:.4rem;"><?= $lang === 'fr' ? 'Actif' : 'نشط' ?></span><?php endif; ?>
                            </div>
                            <form action="<?= href_page('admin/extensions.php') ?>" method="post">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="activate">
                                <input type="hidden" name="type" value="<?= e($type) ?>">
                                <input type="hidden" name="slug" value="<?= e($item['slug']) ?>">
                                <button type="submit" class="btn btn-outline"><?= $lang === 'fr' ? 'Activer' : 'تفعيل' ?></button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    <?php endforeach; ?>
</div>

<div class="panel" style="margin-top:1.5rem; background:#fffaf0;">
    <strong><?= $lang === 'fr' ? 'Sécurité' : 'الأمان' ?></strong>
    <p class="muted" style="margin-bottom:0;"><?= $lang === 'fr' ? 'Les ZIP sont limités à 10 MB et les chemins dangereux sont refusés. Un plugin importé n’est pas exécuté automatiquement.' : 'يتم تحديد ZIP بـ 10 ميغابايت ورفض المسارات الخطرة. لا يتم تشغيل الإضافة المستوردة تلقائياً.' ?></p>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
