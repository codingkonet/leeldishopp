<?php
require __DIR__ . '/../includes/bootstrap.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $phone = trim((string) ($_POST['phone'] ?? '')) ?: null;
    $password = (string) ($_POST['password'] ?? '');

    if (strlen($name) < 2) {
        $error = $lang === 'fr' ? 'Nom trop court.' : 'الاسم قصير جداً.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = $lang === 'fr' ? 'Email invalide.' : 'البريد الإلكتروني غير صالح.';
    } elseif (strlen($password) < 8) {
        $error = $lang === 'fr' ? 'Le mot de passe doit contenir au moins 8 caractères.' : 'يجب أن تتكون كلمة المرور من 8 أحرف على الأقل.';
    } else {
        $result = register_user($name, $email, $password, $phone);
        if ($result['success']) {
            redirect(href_page('account/login.php'));
        }
        $error = $lang === 'fr' ? 'Un compte existe déjà avec cet email.' : 'يوجد حساب بالفعل بهذا البريد الإلكتروني.';
    }
}

require __DIR__ . '/../includes/header.php';
?>

<div class="panel" style="max-width:420px; margin:2rem auto;">
    <p style="color:var(--brand); font-weight:700;">LEBELDISHOP</p>
    <h1><?= $lang === 'fr' ? 'Créer un compte' : 'إنشاء حساب' ?></h1>
    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
    <form action="<?= href_page('account/register.php') ?>" method="post" style="display:flex; flex-direction:column; gap:1rem; margin-top:1rem;">
        <?= csrf_field() ?>
        <input type="text" name="name" required minlength="2" placeholder="<?= $lang === 'fr' ? 'Nom complet' : 'الاسم الكامل' ?>">
        <input type="email" name="email" required placeholder="Email">
        <input type="text" name="phone" placeholder="<?= $lang === 'fr' ? 'Téléphone (optionnel)' : 'الهاتف (اختياري)' ?>">
        <input type="password" name="password" required minlength="8" placeholder="<?= $lang === 'fr' ? 'Mot de passe (8 caractères minimum)' : 'كلمة المرور (8 أحرف على الأقل)' ?>">
        <button type="submit" class="btn btn-dark"><?= $lang === 'fr' ? 'Créer mon compte' : 'إنشاء الحساب' ?></button>
    </form>
    <p class="muted" style="margin-top:1rem; text-align:center;">
        <a href="<?= href_page('account/login.php') ?>" style="color:var(--brand); font-weight:700;"><?= $lang === 'fr' ? 'Déjà client ? Se connecter' : 'لديك حساب؟ تسجيل الدخول' ?></a>
    </p>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
