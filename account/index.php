<?php
require __DIR__ . '/../includes/bootstrap.php';

require_login(href_page('account/login.php'));
$user = current_user();

require __DIR__ . '/../includes/header.php';
?>

<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem;">
    <div>
        <h1><?= $lang === 'fr' ? 'Mon compte' : 'حسابي' ?></h1>
        <p class="muted"><?= e($user['email']) ?></p>
    </div>
    <a href="<?= href_page('account/logout.php') ?>" class="btn btn-outline"><?= e(t('signOut')) ?></a>
</div>

<div class="grid grid-2" style="margin-top:1.5rem;">
    <div class="panel">
        <h3><?= $lang === 'fr' ? 'Historique des commandes' : 'سجل الطلبات' ?></h3>
        <p class="muted"><?= $lang === 'fr' ? 'Consultez vos achats et leurs statuts.' : 'راجع مشترياتك وحالاتها.' ?></p>
        <a href="<?= href_page('account/orders.php') ?>" style="color:var(--brand); font-weight:700;"><?= $lang === 'fr' ? 'Voir' : 'عرض' ?></a>
    </div>
    <div class="panel">
        <h3><?= $lang === 'fr' ? 'Profil' : 'البروفايل' ?></h3>
        <p class="muted"><?= $lang === 'fr' ? 'Vos informations personnelles.' : 'معلوماتك الشخصية.' ?></p>
        <a href="<?= href_page('account/profile.php') ?>" style="color:var(--brand); font-weight:700;"><?= $lang === 'fr' ? 'Voir' : 'عرض' ?></a>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
