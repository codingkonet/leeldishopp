<?php
require __DIR__ . '/../includes/bootstrap.php';
require_admin(href_page('account/login.php'));

$settings = store_settings($pdo);
$error = '';
$themes = ['atlas' => 'Atlas', 'sahara' => 'Sahara', 'zellige' => 'Zellige'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $shopName = trim((string) ($_POST['shop_name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $sloganFr = trim((string) ($_POST['slogan_fr'] ?? ''));
    $sloganAr = trim((string) ($_POST['slogan_ar'] ?? ''));
    $primaryColor = valid_hex_color((string) ($_POST['primary_color'] ?? ''), '#b47d2d');
    $accentColor = valid_hex_color((string) ($_POST['accent_color'] ?? ''), '#1f2937');
    $themeName = array_key_exists($_POST['theme_name'] ?? '', $themes) ? $_POST['theme_name'] : 'atlas';
    $currency = strtoupper(trim((string) ($_POST['currency'] ?? 'MAD')));
    $deliveryFee = max(0, (float) ($_POST['delivery_fee'] ?? 25));
    $freeShipping = max(0, (float) ($_POST['free_shipping_threshold'] ?? 500));
    $phone = trim((string) ($_POST['phone'] ?? '')) ?: null;
    $whatsappNumber = trim((string) ($_POST['whatsapp_number'] ?? '')) ?: null;
    $address = trim((string) ($_POST['address'] ?? '')) ?: null;
    $logoUrl = trim((string) ($_POST['logo_url'] ?? '')) ?: null;
    $faviconUrl = trim((string) ($_POST['favicon_url'] ?? '')) ?: null;
    $seoTitle = trim((string) ($_POST['seo_title'] ?? '')) ?: null;
    $seoDescription = trim((string) ($_POST['seo_description'] ?? '')) ?: null;
    $maintenance = isset($_POST['maintenance_mode']) ? 1 : 0;

    if ($shopName === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $sloganFr === '' || $sloganAr === '') {
        $error = $lang === 'fr' ? 'Vérifiez le nom, l’email et les slogans.' : 'تحقق من الاسم والبريد والشعارات.';
    } else {
        try {
            $uploadedLogo = upload_brand_asset($_FILES['logo_file'] ?? [], 'logo');
            $uploadedFavicon = upload_brand_asset($_FILES['favicon_file'] ?? [], 'favicon');
            $logoUrl = isset($_POST['remove_logo']) ? null : ($uploadedLogo ?: ($logoUrl ?: ($settings['logo_url'] ?? null)));
            $faviconUrl = isset($_POST['remove_favicon']) ? null : ($uploadedFavicon ?: ($faviconUrl ?: ($settings['favicon_url'] ?? null)));

            $stmt = $pdo->prepare('UPDATE settings SET shop_name=?, email=?, phone=?, whatsapp_number=?, address=?, currency=?, delivery_fee=?, free_shipping_threshold=?, slogan_fr=?, slogan_ar=?, primary_color=?, accent_color=?, logo_url=?, favicon_url=?, theme_name=?, maintenance_mode=?, seo_title=?, seo_description=? WHERE id = ?');
            $stmt->execute([$shopName, $email, $phone, $whatsappNumber, $address, $currency ?: 'MAD', $deliveryFee, $freeShipping, $sloganFr, $sloganAr, $primaryColor, $accentColor, $logoUrl, $faviconUrl, $themeName, $maintenance, $seoTitle, $seoDescription, $settings['id'] ?? 1]);
            flash('success', $lang === 'fr' ? 'Paramètres enregistrés.' : 'تم حفظ الإعدادات.');
            redirect(href_page('admin/settings.php'));
        } catch (Throwable $exception) {
            $error = $exception->getMessage();
        }
    }
}

require __DIR__ . '/includes/admin_header.php';
?>

<h1><?= $lang === 'fr' ? 'Paramètres de la boutique' : 'إعدادات المتجر' ?></h1>
<p class="muted"><?= $lang === 'fr' ? 'Modifiez l’identité et les options principales de votre boutique.' : 'عدّل هوية المتجر وخياراته الرئيسية.' ?></p>
<?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

<form action="<?= href_page('admin/settings.php') ?>" method="post" enctype="multipart/form-data" class="panel form-grid cols-2" style="margin-top:1rem;">
    <?= csrf_field() ?>
    <h2 style="grid-column:span 2;"><?= $lang === 'fr' ? 'Identité' : 'الهوية' ?></h2>
    <input type="text" name="shop_name" required value="<?= e($settings['shop_name'] ?? '') ?>" placeholder="<?= $lang === 'fr' ? 'Titre de la boutique' : 'عنوان المتجر' ?>">
    <input type="email" name="email" required value="<?= e($settings['email'] ?? '') ?>" placeholder="Email">
    <input type="text" name="slogan_fr" required value="<?= e($settings['slogan_fr'] ?? '') ?>" placeholder="Slogan français">
    <input type="text" name="slogan_ar" required value="<?= e($settings['slogan_ar'] ?? '') ?>" placeholder="الشعار بالعربية">
    <label>Logo depuis votre ordinateur <input type="file" name="logo_file" accept="image/png,image/jpeg,image/webp,image/x-icon"></label>
    <label>Favicon depuis votre ordinateur <input type="file" name="favicon_file" accept="image/png,image/jpeg,image/webp,image/x-icon"></label>
    <input type="url" name="logo_url" value="<?= e($settings['logo_url'] ?? '') ?>" placeholder="Ou URL du logo">
    <input type="url" name="favicon_url" value="<?= e($settings['favicon_url'] ?? '') ?>" placeholder="Ou URL du favicon">
    <label><input type="checkbox" name="remove_logo"> Supprimer le logo actuel</label>
    <label><input type="checkbox" name="remove_favicon"> Supprimer le favicon actuel</label>
    <input type="tel" name="phone" value="<?= e($settings['phone'] ?? '') ?>" placeholder="Téléphone">
    <input type="tel" name="whatsapp_number" value="<?= e($settings['whatsapp_number'] ?? '') ?>" placeholder="WhatsApp (ex: +212600000000)">
    <input type="text" name="address" value="<?= e($settings['address'] ?? '') ?>" placeholder="Adresse">

    <h2 style="grid-column:span 2;"><?= $lang === 'fr' ? 'Apparence' : 'المظهر' ?></h2>
    <select name="theme_name">
        <?php foreach ($themes as $value => $label): ?><option value="<?= e($value) ?>" <?= ($settings['theme_name'] ?? 'atlas') === $value ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?>
    </select>
    <input type="text" name="currency" maxlength="10" value="<?= e($settings['currency'] ?? 'MAD') ?>" placeholder="Devise">
    <label>Couleur principale <input type="color" name="primary_color" value="<?= e(valid_hex_color((string) ($settings['primary_color'] ?? ''), '#b47d2d')) ?>"></label>
    <label>Couleur secondaire <input type="color" name="accent_color" value="<?= e(valid_hex_color((string) ($settings['accent_color'] ?? ''), '#1f2937')) ?>"></label>

    <h2 style="grid-column:span 2;"><?= $lang === 'fr' ? 'Livraison et SEO' : 'التوصيل و SEO' ?></h2>
    <input type="number" min="0" step="0.01" name="delivery_fee" value="<?= e((string) ($settings['delivery_fee'] ?? 25)) ?>" placeholder="Frais de livraison">
    <input type="number" min="0" step="0.01" name="free_shipping_threshold" value="<?= e((string) ($settings['free_shipping_threshold'] ?? 500)) ?>" placeholder="Seuil livraison gratuite">
    <input type="text" name="seo_title" value="<?= e($settings['seo_title'] ?? '') ?>" placeholder="Meta title" style="grid-column:span 2;">
    <textarea name="seo_description" placeholder="Meta description" style="grid-column:span 2;"><?= e($settings['seo_description'] ?? '') ?></textarea>
    <label style="grid-column:span 2;"><input type="checkbox" name="maintenance_mode" <?= !empty($settings['maintenance_mode']) ? 'checked' : '' ?>> <?= $lang === 'fr' ? 'Mode maintenance' : 'وضع الصيانة' ?></label>
    <button type="submit" class="btn btn-brand" style="grid-column:span 2;"><?= $lang === 'fr' ? 'Enregistrer les paramètres' : 'حفظ الإعدادات' ?></button>
</form>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
