<?php
require __DIR__ . '/includes/bootstrap.php';

$services = $pdo->query('SELECT * FROM services WHERE available = 1 ORDER BY created_at DESC')->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<h1><?= $lang === 'fr' ? 'Services marocains' : 'خدمات مغربية' ?></h1>

<div class="grid grid-2" style="margin-top:1.5rem;">
    <?php foreach ($services as $service): ?>
        <div class="panel">
            <p class="muted" style="text-transform:uppercase; font-size:.8rem;">Service</p>
            <h2><?= e($lang === 'fr' ? $service['title_fr'] : $service['title_ar']) ?></h2>
            <p><?= e($lang === 'fr' ? $service['description_fr'] : $service['description_ar']) ?></p>
            <div style="display:flex; justify-content:space-between;">
                <span class="muted"><?= e($service['location'] ?? '') ?></span>
                <span class="badge" style="background:#fdf4d8; color:#8a5a12;"><?= format_price((float) $service['price'], $lang) ?></span>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
