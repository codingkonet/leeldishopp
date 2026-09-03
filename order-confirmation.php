<?php
require __DIR__ . '/includes/bootstrap.php';

$orderNumber = trim((string) ($_GET['order'] ?? ''));
$total = isset($_GET['total']) ? (float) $_GET['total'] : null;

require __DIR__ . '/includes/header.php';
?>

<div class="panel" style="text-align:center; max-width:640px; margin:2rem auto; background:#ecfdf5; border-color:#a7f3d0;">
    <div style="width:64px; height:64px; border-radius:999px; background:#059669; color:#fff; font-size:1.8rem; display:flex; align-items:center; justify-content:center; margin:0 auto;">✓</div>
    <h1 style="margin-top:1rem;"><?= $lang === 'fr' ? 'Commande confirmée' : 'تم تأكيد الطلب' ?></h1>
    <p><?= $lang === 'fr'
        ? 'Votre commande a bien été enregistrée. Vous recevrez une confirmation sous peu.'
        : 'تم تسجيل طلبك بنجاح. ستتلقى تأكيداً قريباً.' ?></p>
    <div class="panel" style="text-align:left; margin-top:1rem;">
        <p style="display:flex; justify-content:space-between;"><span><?= $lang === 'fr' ? 'Numéro de commande' : 'رقم الطلب' ?></span><strong><?= $orderNumber !== '' ? e('#' . $orderNumber) : '—' ?></strong></p>
        <p style="display:flex; justify-content:space-between;"><span><?= e(t('total')) ?></span><strong><?= $total !== null ? format_price($total, $lang) : '—' ?></strong></p>
        <p style="display:flex; justify-content:space-between;"><span><?= $lang === 'fr' ? 'Paiement' : 'الدفع' ?></span><strong>COD</strong></p>
    </div>
    <div style="display:flex; justify-content:center; gap:1rem; margin-top:1.5rem;">
        <a href="<?= href_page('index.php') ?>" class="btn btn-dark"><?= $lang === 'fr' ? 'Retour à l’accueil' : 'العودة للرئيسية' ?></a>
        <a href="<?= href_page('account/orders.php') ?>" class="btn btn-outline"><?= $lang === 'fr' ? 'Suivi de commande' : 'تتبع الطلب' ?></a>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
