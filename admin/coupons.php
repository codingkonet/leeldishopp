<?php
require __DIR__ . '/../includes/bootstrap.php';
require_admin(href_page('account/login.php'));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    if (($_POST['action'] ?? '') === 'create') {
        $code = strtoupper(trim((string) ($_POST['code'] ?? '')));
        $type = $_POST['type'] === 'PERCENTAGE' ? 'PERCENTAGE' : 'FIXED';
        $value = max(0, (float) ($_POST['value'] ?? 0));
        $minimum = max(0, (float) ($_POST['minimum_order'] ?? 0));
        $maxUses = trim((string) ($_POST['max_uses'] ?? '')) !== '' ? max(1, (int) $_POST['max_uses']) : null;
        if ($code === '' || $value <= 0 || ($type === 'PERCENTAGE' && $value > 100)) {
            flash('error', $lang === 'fr' ? 'Vérifiez le code et la valeur.' : 'تحقق من الرمز والقيمة.');
        } else {
            $stmt = $pdo->prepare('INSERT INTO coupons (code, type, value, minimum_order, max_uses) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$code, $type, $value, $minimum, $maxUses]);
            flash('success', $lang === 'fr' ? 'Coupon créé.' : 'تم إنشاء القسيمة.');
        }
    } elseif (($_POST['action'] ?? '') === 'toggle') {
        $stmt = $pdo->prepare('UPDATE coupons SET is_active = NOT is_active WHERE id = ?');
        $stmt->execute([(int) $_POST['id']]);
    }
    redirect(href_page('admin/coupons.php'));
}
$coupons = $pdo->query('SELECT * FROM coupons ORDER BY id DESC')->fetchAll();
require __DIR__ . '/includes/admin_header.php';
?>
<h1><?= $lang === 'fr' ? 'Coupons et promotions' : 'القسائم والعروض' ?></h1>
<form method="post" class="panel form-grid cols-2" style="margin:1rem 0;">
    <?= csrf_field() ?><input type="hidden" name="action" value="create">
    <input name="code" required placeholder="Code (ex: MAROC10)">
    <select name="type"><option value="PERCENTAGE">Pourcentage</option><option value="FIXED">Montant fixe</option></select>
    <input type="number" name="value" min="0.01" step="0.01" required placeholder="Valeur">
    <input type="number" name="minimum_order" min="0" step="0.01" placeholder="Commande minimum">
    <input type="number" name="max_uses" min="1" placeholder="Nombre maximum (optionnel)">
    <button class="btn btn-brand" type="submit">+ <?= $lang === 'fr' ? 'Créer le coupon' : 'إنشاء القسيمة' ?></button>
</form>
<table><thead><tr><th>Code</th><th>Type</th><th>Valeur</th><th>Utilisations</th><th>Statut</th><th></th></tr></thead><tbody>
<?php foreach ($coupons as $coupon): ?><tr><td><strong><?= e($coupon['code']) ?></strong></td><td><?= e($coupon['type']) ?></td><td><?= e((string) $coupon['value']) ?></td><td><?= (int) $coupon['used_count'] ?> / <?= $coupon['max_uses'] ? (int) $coupon['max_uses'] : '∞' ?></td><td><?= $coupon['is_active'] ? 'Actif' : 'Inactif' ?></td><td><form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?= (int) $coupon['id'] ?>"><button class="btn btn-outline" type="submit">Activer/Désactiver</button></form></td></tr><?php endforeach; ?>
</tbody></table>
<?php require __DIR__ . '/includes/admin_footer.php'; ?>
