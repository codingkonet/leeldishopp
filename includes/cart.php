<?php
declare(strict_types=1);

function cart_items(): array
{
    return $_SESSION['cart'] ?? [];
}

function cart_add(int $productId, int $quantity = 1): void
{
    $_SESSION['cart'][$productId] = ($_SESSION['cart'][$productId] ?? 0) + $quantity;
}

function cart_update(int $productId, int $quantity): void
{
    if ($quantity < 1) {
        cart_remove($productId);
        return;
    }
    $_SESSION['cart'][$productId] = $quantity;
}

function cart_remove(int $productId): void
{
    unset($_SESSION['cart'][$productId]);
}

function cart_clear(): void
{
    unset($_SESSION['cart']);
}

function cart_count(): int
{
    return array_sum(cart_items());
}

function cart_lines(PDO $pdo): array
{
    $items = cart_items();
    if (empty($items)) {
        return [];
    }

    $ids = array_keys($items);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $products = $stmt->fetchAll();

    $lines = [];
    foreach ($products as $product) {
        $qty = (int) ($items[$product['id']] ?? 0);
        if ($qty < 1) {
            continue;
        }
        $lines[] = [
            'product' => $product,
            'quantity' => $qty,
            'lineTotal' => $qty * (float) $product['price'],
        ];
    }

    return $lines;
}

function cart_subtotal(PDO $pdo): float
{
    $subtotal = 0.0;
    foreach (cart_lines($pdo) as $line) {
        $subtotal += $line['lineTotal'];
    }
    return $subtotal;
}

function cart_has_digital(PDO $pdo): bool
{
    foreach (cart_lines($pdo) as $line) {
        if ($line['product']['product_type'] === 'DIGITAL') return true;
    }
    return false;
}
