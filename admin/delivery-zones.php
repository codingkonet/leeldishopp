<?php
require __DIR__ . '/../includes/bootstrap.php';
require_admin(href_page('account/login.php'));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $city = trim((string) ($_POST['city'] ?? ''));
    $region = trim((string) ($_POST['region'] ?? '')) ?: null;
    $standard = max(0, (float) ($_POST['standard_fee'] ?? 25));
    $express = max(0, (float) ($_POST['express_fee'] ?? 45));
    if ($city !== '') {
        $stmt = $pdo->prepare('INSERT INTO delivery_zones (city, region, standard_fee, express_fee) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE region=VALUES(region), standard_fee=VALUES(standard_fee), express_fee=VALUES(express_fee), is_active=1');
        $stmt->execute([$city, $region, $standard, $express]);
        flash('success', $lang === 'fr' ? 'Zone enregistrée.' : 'تم حفظ منطقة التوصيل.');
    }
    redirect(href_page('admin/delivery-zones.php'));
}
$zones = $pdo->query('SELECT * FROM delivery_zones ORDER BY city')->fetchAll();
require __DIR__ . '/includes/admin_header.php';
?>
<h1><?= $lang === 'fr' ? 'Zones de livraison' : 'مناطق التوصيل' ?></h1>
<form method="post" class="panel form-grid cols-2" style="margin:1rem 0;"><?= csrf_field() ?><input name="city" required placeholder="Ville"><input name="region" placeholder="Région"><input type="number" min="0" step="0.01" name="standard_fee" value="25" placeholder="Standard"><input type="number" min="0" step="0.01" name="express_fee" value="45" placeholder="Express"><button class="btn btn-brand" type="submit">+ <?= $lang === 'fr' ? 'Enregistrer la zone' : 'حفظ المنطقة' ?></button></form>
<table><thead><tr><th>Ville</th><th>Région</th><th>Standard</th><th>Express</th><th>Statut</th></tr></thead><tbody>
<?php foreach ($zones as $zone): ?><tr><td><?= e($zone['city']) ?></td><td><?= e($zone['region'] ?? '') ?></td><td><?= format_price((float) $zone['standard_fee'], $lang) ?></td><td><?= format_price((float) $zone['express_fee'], $lang) ?></td><td><?= $zone['is_active'] ? 'Actif' : 'Inactif' ?></td></tr><?php endforeach; ?>
</tbody></table>
<?php require __DIR__ . '/includes/admin_footer.php'; ?>
