<?php
require __DIR__ . '/../includes/bootstrap.php';

require_login(href_page('account/login.php'));
$user = current_user();

require __DIR__ . '/../includes/header.php';
?>

<h1><?= $lang === 'fr' ? 'Profil' : 'البروفايل' ?></h1>

<div class="panel" style="max-width:640px; margin-top:1.5rem;">
    <p style="display:flex; justify-content:space-between; border-bottom:1px solid var(--border); padding-bottom:.6rem;"><span class="muted"><?= $lang === 'fr' ? 'Nom' : 'الاسم' ?></span><strong><?= e($user['name'] ?? '—') ?></strong></p>
    <p style="display:flex; justify-content:space-between; border-bottom:1px solid var(--border); padding:.6rem 0;"><span class="muted">Email</span><strong><?= e($user['email']) ?></strong></p>
    <p style="display:flex; justify-content:space-between; padding-top:.6rem;"><span class="muted"><?= $lang === 'fr' ? 'Rôle' : 'الدور' ?></span><strong><?= e($user['role']) ?></strong></p>
</div>

<a href="<?= href_page('account/logout.php') ?>" class="btn btn-outline" style="margin-top:1rem;"><?= e(t('signOut')) ?></a>

<?php require __DIR__ . '/../includes/footer.php'; ?>
