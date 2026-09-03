<?php
declare(strict_types=1);

function find_coupon(PDO $pdo, string $code, float $subtotal): ?array
{
    $code = strtoupper(trim($code));
    if ($code === '') return null;
    $stmt = $pdo->prepare('SELECT * FROM coupons WHERE code = ? AND is_active = 1 AND (active_from IS NULL OR active_from <= NOW()) AND (active_until IS NULL OR active_until >= NOW()) AND (max_uses IS NULL OR used_count < max_uses) AND minimum_order <= ? LIMIT 1');
    $stmt->execute([$code, $subtotal]);
    return $stmt->fetch() ?: null;
}

function coupon_discount(array $coupon, float $subtotal): float
{
    $discount = $coupon['type'] === 'PERCENTAGE'
        ? $subtotal * ((float) $coupon['value'] / 100)
        : (float) $coupon['value'];
    return min($subtotal, max(0, round($discount, 2)));
}

function delivery_fee(PDO $pdo, string $city, string $method, float $subtotal): float
{
    if ($subtotal >= FREE_SHIPPING_THRESHOLD) return 0.0;
    $method = in_array($method, ['STANDARD', 'EXPRESS', 'PICKUP'], true) ? $method : 'STANDARD';
    if ($method === 'PICKUP') return 0.0;
    $stmt = $pdo->prepare('SELECT standard_fee, express_fee FROM delivery_zones WHERE city = ? AND is_active = 1 LIMIT 1');
    $stmt->execute([trim($city)]);
    $zone = $stmt->fetch();
    if ($zone) return (float) ($method === 'EXPRESS' ? $zone['express_fee'] : $zone['standard_fee']);
    return DEFAULT_SHIPPING_FEES[$method];
}
