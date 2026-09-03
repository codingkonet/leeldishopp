<?php
require __DIR__ . '/../../includes/bootstrap.php';

require_admin(href_page('account/login.php'));
$user = current_user();
?>
<!DOCTYPE html>
<html lang="<?= e($lang) ?>" dir="<?= $isRtl ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Admin</title>
    <link rel="stylesheet" href="<?= href_asset('assets/css/style.css') ?>">
</head>
<body class="<?= $isRtl ? 'rtl' : 'ltr' ?>">
<div class="admin-shell">
    <nav class="admin-nav">
        <a href="<?= href_page('admin/index.php') ?>" style="font-weight:900; font-size:1.2rem; color:#fff; display:block; margin-bottom:1rem;">
            Lebeldi<span style="color:var(--brand);">Shop</span> <small style="color:#94a3b8;">ADMIN</small>
        </a>
        <a href="<?= href_page('admin/index.php') ?>"><?= $lang === 'fr' ? 'Vue d’ensemble' : 'نظرة عامة' ?></a>
        <a href="<?= href_page('admin/orders.php') ?>"><?= $lang === 'fr' ? 'Commandes' : 'الطلبات' ?></a>
        <a href="<?= href_page('admin/products.php') ?>"><?= $lang === 'fr' ? 'Produits' : 'المنتجات' ?></a>
        <a href="<?= href_page('admin/import-products.php') ?>"><?= $lang === 'fr' ? 'Importer produits' : 'استيراد المنتجات' ?></a>
        <a href="<?= href_page('admin/extensions.php') ?>"><?= $lang === 'fr' ? 'Plugins et thèmes' : 'الإضافات والقوالب' ?></a>
        <a href="<?= href_page('account/logout.php') ?>" style="margin-top:1.5rem; border-top:1px solid rgba(255,255,255,.1); padding-top:1rem;"><?= e(t('signOut')) ?></a>
    </nav>
    <main class="admin-main">
    <?php if ($message = flash('success')): ?><div class="alert alert-success"><?= e($message) ?></div><?php endif; ?>
    <?php if ($message = flash('error')): ?><div class="alert alert-error"><?= e($message) ?></div><?php endif; ?>
