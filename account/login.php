<?php
require __DIR__ . '/../includes/bootstrap.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    $result = attempt_login($email, $password);
    if ($result['success']) {
        redirect(href_page('account/index.php'));
    }

    $error = match ($result['error']) {
        'locked' => $lang === 'fr' ? 'Compte temporairement verrouillé. Réessayez plus tard.' : 'الحساب مقفل مؤقتاً. حاول لاحقاً.',
        'inactive' => $lang === 'fr' ? 'Ce compte est désactivé.' : 'هذا الحساب معطل.',
        default => $lang === 'fr' ? 'Email ou mot de passe incorrect.' : 'البريد الإلكتروني أو كلمة المرور غير صحيحين.',
    };
}

require __DIR__ . '/../includes/header.php';
?>

<div class="panel" style="max-width:420px; margin:2rem auto;">
    <p style="color:var(--brand); font-weight:700;">LEBELDISHOP</p>
    <h1><?= $lang === 'fr' ? 'Connexion' : 'تسجيل الدخول' ?></h1>
    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
    <form action="<?= href_page('account/login.php') ?>" method="post" style="display:flex; flex-direction:column; gap:1rem; margin-top:1rem;">
        <?= csrf_field() ?>
        <input type="email" name="email" required placeholder="Email">
        <input type="password" name="password" required minlength="8" placeholder="<?= $lang === 'fr' ? 'Mot de passe' : 'كلمة المرور' ?>">
        <button type="submit" class="btn btn-dark"><?= $lang === 'fr' ? 'Se connecter' : 'دخول' ?></button>
    </form>
    <p class="muted" style="margin-top:1rem; text-align:center;">
        <?= $lang === 'fr' ? 'Nouveau client ?' : 'عميل جديد؟' ?>
        <a href="<?= href_page('account/register.php') ?>" style="color:var(--brand); font-weight:700;"><?= $lang === 'fr' ? 'Créer un compte' : 'إنشاء حساب' ?></a>
    </p>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
