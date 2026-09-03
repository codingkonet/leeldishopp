<!DOCTYPE html>
<html lang="<?= e($lang) ?>" dir="<?= $isRtl ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($settings['seo_title'] ?: $settings['shop_name']) ?></title>
    <link rel="stylesheet" href="<?= href_asset('assets/css/style.css') ?>">
    <?php $themeName = preg_match('/^[a-z0-9-]+$/', (string) ($settings['theme_name'] ?? '')) ? $settings['theme_name'] : 'atlas'; ?>
    <link rel="stylesheet" href="<?= href_asset('themes/' . $themeName . '.css') ?>">
        <?php if (!empty($settings['favicon_url']) && filter_var($settings['favicon_url'], FILTER_VALIDATE_URL)): ?>
            <link rel="icon" href="<?= e($settings['favicon_url']) ?>">
        <?php endif; ?>
</head>
<body class="<?= $isRtl ? 'rtl' : 'ltr' ?>" style="--brand:<?= e(valid_hex_color((string) ($settings['primary_color'] ?? ''), '#b47d2d')) ?>;--dark:<?= e(valid_hex_color((string) ($settings['accent_color'] ?? ''), '#1f2937')) ?>;">
<header class="site-header">
    <div class="container header-inner">
        <a href="<?= href_page('index.php') ?>" class="brand">
            <span class="brand-mark">L</span>
            <span>
                <span class="brand-name"><?= e($settings['shop_name'] ?? APP_NAME) ?></span>
                <small class="brand-slogan"><?= e($lang === 'fr' ? ($settings['slogan_fr'] ?? t('slogan')) : ($settings['slogan_ar'] ?? t('slogan'))) ?></small>
            </span>
        </a>

        <form action="<?= href_page('products.php') ?>" method="get" class="search-form">
            <input type="text" name="q" placeholder="<?= e(t('search')) ?>" value="<?= e($_GET['q'] ?? '') ?>">
        </form>

        <nav class="main-nav">
            <a href="<?= href_page('index.php') ?>"><?= e(t('home')) ?></a>
            <a href="<?= href_page('products.php') ?>"><?= e(t('shop')) ?></a>
            <a href="<?= href_page('services.php') ?>"><?= e(t('services')) ?></a>
            <a href="<?= href_page('account/index.php') ?>"><?= e(t('account')) ?></a>
        </nav>

        <div class="header-actions">
            <a class="pill" href="?lang=<?= $lang === 'fr' ? 'ar' : 'fr' ?>"><?= $lang === 'fr' ? 'العربية' : 'FR' ?></a>

            <?php if (is_admin()): ?>
                <a class="pill" href="<?= href_page('admin/index.php') ?>"><?= e(t('admin')) ?></a>
            <?php endif; ?>

            <?php if (is_logged_in()): ?>
                <a class="pill" href="<?= href_page('account/logout.php') ?>"><?= e(t('signOut')) ?></a>
            <?php else: ?>
                <a class="pill" href="<?= href_page('account/login.php') ?>"><?= e(t('signIn')) ?></a>
            <?php endif; ?>

            <a class="cart-link" href="<?= href_page('cart.php') ?>">
                🛒
                <?php $count = cart_count(); if ($count > 0): ?>
                    <span class="cart-badge"><?= (int) $count ?></span>
                <?php endif; ?>
            </a>
        </div>
    </div>
</header>
<main class="container page-content">
<?php if ($message = flash('success')): ?>
    <div class="alert alert-success"><?= e($message) ?></div>
<?php endif; ?>
<?php if ($message = flash('error')): ?>
    <div class="alert alert-error"><?= e($message) ?></div>
<?php endif; ?>
