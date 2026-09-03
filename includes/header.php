<!DOCTYPE html>
<html lang="<?= e($lang) ?>" dir="<?= $isRtl ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — <?= e(t('slogan')) ?></title>
    <link rel="stylesheet" href="<?= href_asset('assets/css/style.css') ?>">
</head>
<body class="<?= $isRtl ? 'rtl' : 'ltr' ?>">
<header class="site-header">
    <div class="container header-inner">
        <a href="<?= href_page('index.php') ?>" class="brand">
            <span class="brand-mark">L</span>
            <span>
                <span class="brand-name"><?= e(APP_NAME) ?></span>
                <small class="brand-slogan"><?= e(t('slogan')) ?></small>
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
