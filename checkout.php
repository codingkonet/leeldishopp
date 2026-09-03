<?php
require __DIR__ . '/includes/bootstrap.php';

$lines = cart_lines($pdo);
$subtotal = cart_subtotal($pdo);
$error = '';
$coupon = !empty($_SESSION['coupon_code']) ? find_coupon($pdo, (string) $_SESSION['coupon_code'], $subtotal) : null;
$discount = $coupon ? coupon_discount($coupon, $subtotal) : 0.0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'apply_coupon') {
    verify_csrf();
    $candidate = find_coupon($pdo, (string) ($_POST['coupon_code'] ?? ''), $subtotal);
    if (!$candidate) {
        $error = $lang === 'fr' ? 'Code promo invalide ou expiré.' : 'رمز الخصم غير صالح أو منتهي.';
    } else {
        $_SESSION['coupon_code'] = strtoupper(trim((string) $_POST['coupon_code']));
        redirect(href_page('checkout.php'));
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') !== 'apply_coupon') {
    verify_csrf();

    if (empty($lines)) {
        $error = $lang === 'fr' ? 'Votre panier est vide.' : 'سلتك فارغة.';
    } else {
        $customerName = trim((string) ($_POST['customer_name'] ?? ''));
        $customerPhone = trim((string) ($_POST['customer_phone'] ?? ''));
        $customerEmail = trim((string) ($_POST['customer_email'] ?? '')) ?: null;
        $deliveryAddress = trim((string) ($_POST['delivery_address'] ?? ''));
        $city = trim((string) ($_POST['city'] ?? ''));
        $region = trim((string) ($_POST['region'] ?? '')) ?: null;
        $postalCode = trim((string) ($_POST['postal_code'] ?? '')) ?: null;
        $deliveryNotes = trim((string) ($_POST['delivery_notes'] ?? '')) ?: null;
        $shippingMethod = in_array($_POST['shipping_method'] ?? '', ['STANDARD', 'EXPRESS', 'PICKUP'], true)
            ? $_POST['shipping_method']
            : 'STANDARD';

        if (strlen($customerName) < 2 || strlen($customerPhone) < 8 || strlen($deliveryAddress) < 8 || $city === '') {
            $error = $lang === 'fr' ? 'Merci de compléter tous les champs obligatoires.' : 'يرجى ملء جميع الحقول المطلوبة.';
        } elseif ($customerEmail && !filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
            $error = $lang === 'fr' ? 'Email invalide.' : 'البريد الإلكتروني غير صالح.';
        } else {
            try {
                $pdo->beginTransaction();

                foreach ($lines as $line) {
                    $stmt = $pdo->prepare('UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?');
                    $stmt->execute([$line['quantity'], $line['product']['id'], $line['quantity']]);
                    if ($stmt->rowCount() !== 1) {
                        throw new RuntimeException($lang === 'fr' ? 'Stock insuffisant pour un produit.' : 'المخزون غير كافٍ لأحد المنتجات.');
                    }
                }

                $coupon = !empty($_SESSION['coupon_code']) ? find_coupon($pdo, (string) $_SESSION['coupon_code'], $subtotal) : null;
                $discount = $coupon ? coupon_discount($coupon, $subtotal) : 0.0;
                $shippingFee = $subtotal - $discount >= FREE_SHIPPING_THRESHOLD ? 0.0 : DEFAULT_SHIPPING_FEES[$shippingMethod];
                $total = $subtotal - $discount + $shippingFee;
                $orderNumber = generate_order_number();
                $user = current_user();

                $orderStmt = $pdo->prepare('INSERT INTO orders (order_number, user_id, customer_name, customer_phone, customer_email, delivery_address, city, region, postal_code, delivery_notes, shipping_method, subtotal, discount, coupon_code, shipping_cost, total) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
                $orderStmt->execute([
                    $orderNumber,
                    $user['id'] ?? null,
                    $customerName,
                    $customerPhone,
                    $customerEmail,
                    $deliveryAddress,
                    $city,
                    $region,
                    $postalCode,
                    $deliveryNotes,
                    $shippingMethod,
                    $subtotal,
                    $discount,
                    $coupon['code'] ?? null,
                    $shippingFee,
                    $total,
                ]);
                $orderId = (int) $pdo->lastInsertId();

                $itemStmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?,?,?,?)');
                foreach ($lines as $line) {
                    $itemStmt->execute([$orderId, $line['product']['id'], $line['quantity'], $line['product']['price']]);
                }
                if ($coupon) {
                    $couponStmt = $pdo->prepare('UPDATE coupons SET used_count = used_count + 1 WHERE id = ?');
                    $couponStmt->execute([$coupon['id']]);
                }

                $pdo->commit();
                cart_clear();
                unset($_SESSION['coupon_code']);
                $emailOrder = ['order_number' => $orderNumber, 'customer_name' => $customerName, 'customer_email' => $customerEmail, 'total' => $total];
                send_order_email($emailOrder);
                notify_admin_new_order($emailOrder);

                redirect(href_page('order-confirmation.php') . '?order=' . urlencode($orderNumber) . '&total=' . urlencode((string) $total));
            } catch (Throwable $exception) {
                $pdo->rollBack();
                $error = $exception->getMessage();
            }
        }
    }
}

require __DIR__ . '/includes/header.php';
?>

<h1><?= e(t('checkout')) ?></h1>

<form action="<?= href_page('checkout.php') ?>" method="post" class="grid" style="grid-template-columns: 1.3fr .7fr; margin-top:1.5rem;">
    <?= csrf_field() ?>
    <div style="display:flex; flex-direction:column; gap:1.2rem;">
        <div class="panel">
            <h2><?= $lang === 'fr' ? 'Informations client' : 'معلومات العميل' ?></h2>
            <div class="form-grid cols-2" style="margin-top:1rem;">
                <input type="text" name="customer_name" required minlength="2" placeholder="<?= $lang === 'fr' ? 'Nom complet' : 'الاسم الكامل' ?>">
                <input type="text" name="customer_phone" required minlength="8" placeholder="<?= $lang === 'fr' ? 'Téléphone' : 'الهاتف' ?>">
                <input type="email" name="customer_email" placeholder="Email (<?= $lang === 'fr' ? 'optionnel' : 'اختياري' ?>)" style="grid-column: span 2;">
                <input type="text" name="delivery_address" required minlength="8" placeholder="<?= $lang === 'fr' ? 'Adresse' : 'العنوان' ?>" style="grid-column: span 2;">
                <input type="text" name="city" required placeholder="<?= $lang === 'fr' ? 'Ville' : 'المدينة' ?>">
                <input type="text" name="region" placeholder="<?= $lang === 'fr' ? 'Région' : 'المنطقة' ?>">
                <input type="text" name="postal_code" placeholder="<?= $lang === 'fr' ? 'Code postal' : 'الرمز البريدي' ?>">
                <input type="text" name="delivery_notes" placeholder="<?= $lang === 'fr' ? 'Instructions (optionnel)' : 'تعليمات (اختياري)' ?>">
            </div>
        </div>

        <div class="panel">
            <h2><?= $lang === 'fr' ? 'Livraison' : 'التوصيل' ?></h2>
            <?php foreach (['STANDARD' => ['Livraison standard', 'التوصيل العادي'], 'EXPRESS' => ['Livraison express', 'التوصيل السريع'], 'PICKUP' => ['Retrait en point relais', 'الاستلام من نقطة التوصيل']] as $value => $labels): ?>
                <label style="display:flex; justify-content:space-between; padding:.8rem; border:1px solid var(--border); border-radius:.8rem; margin-top:.6rem;">
                    <span><input type="radio" name="shipping_method" value="<?= $value ?>" <?= $value === 'STANDARD' ? 'checked' : '' ?>> <?= $lang === 'fr' ? $labels[0] : $labels[1] ?></span>
                    <strong><?= DEFAULT_SHIPPING_FEES[$value] > 0 ? format_price(DEFAULT_SHIPPING_FEES[$value], $lang) : ($lang === 'fr' ? 'Gratuit' : 'مجاني') ?></strong>
                </label>
            <?php endforeach; ?>
        </div>

        <div class="panel" style="background:#ecfdf5;">
            <strong>COD — <?= e(t('secureCod')) ?></strong>
            <p class="muted"><?= e(t('codInfo')) ?></p>
        </div>
    </div>

    <aside class="panel">
        <h2><?= $lang === 'fr' ? 'Résumé commande' : 'ملخص الطلب' ?></h2>
        <?php foreach ($lines as $line): $p = $line['product']; ?>
            <p style="display:flex; justify-content:space-between;"><span><?= e($lang === 'fr' ? $p['name_fr'] : $p['name_ar']) ?> x<?= (int) $line['quantity'] ?></span><span><?= format_price($line['lineTotal'], $lang) ?></span></p>
        <?php endforeach; ?>
        <form action="<?= href_page('checkout.php') ?>" method="post" style="display:flex; gap:.5rem; margin:1rem 0;">
            <?= csrf_field() ?><input type="hidden" name="action" value="apply_coupon"><input name="coupon_code" placeholder="<?= $lang === 'fr' ? 'Code promo' : 'رمز الخصم' ?>" value="<?= e($_SESSION['coupon_code'] ?? '') ?>"><button class="btn btn-outline" type="submit">OK</button>
        </form>
        <?php if ($coupon): ?><p style="display:flex; justify-content:space-between; color:#047857;"><span>Coupon <?= e($coupon['code']) ?></span><span>-<?= format_price($discount, $lang) ?></span></p><?php endif; ?>
        <p style="display:flex; justify-content:space-between; font-weight:800; border-top:1px solid var(--border); padding-top:.6rem;"><span><?= e(t('total')) ?></span><span><?= format_price($subtotal - $discount, $lang) ?> + <?= $lang === 'fr' ? 'livraison' : 'التوصيل' ?></span></p>
        <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
        <button type="submit" class="btn btn-brand" style="width:100%; justify-content:center; margin-top:1rem;" <?= empty($lines) ? 'disabled' : '' ?>><?= $lang === 'fr' ? 'Confirmer la commande' : 'تأكيد الطلب' ?></button>
    </aside>
</form>

<?php require __DIR__ . '/includes/footer.php'; ?>
