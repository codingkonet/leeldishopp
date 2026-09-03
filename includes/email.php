<?php
declare(strict_types=1);

function send_order_email(array $order): bool
{
    $recipient = $order['customer_email'] ?? null;
    if (!$recipient || !filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    $from = getenv('MAIL_FROM') ?: 'shop@lebeldishop.com';
    $subject = APP_NAME . ' — Commande ' . $order['order_number'];
    $body = "Bonjour {$order['customer_name']},\n\nVotre commande {$order['order_number']} a bien été enregistrée.\nTotal: " . format_price((float) $order['total']) . "\nPaiement: COD\n\nMerci,\n" . APP_NAME;
    $headers = "From: " . $from . "\r\nContent-Type: text/plain; charset=UTF-8\r\n";
    return mail($recipient, $subject, $body, $headers);
}

function notify_admin_new_order(array $order): bool
{
    $recipient = getenv('ADMIN_EMAIL') ?: 'shop@lebeldishop.com';
    if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) return false;
    $subject = APP_NAME . ' — Nouvelle commande ' . $order['order_number'];
    $body = "Nouvelle commande {$order['order_number']}\nClient: {$order['customer_name']}\nTotal: " . format_price((float) $order['total']);
    $from = getenv('MAIL_FROM') ?: 'shop@lebeldishop.com';
    return mail($recipient, $subject, $body, "From: {$from}\r\nContent-Type: text/plain; charset=UTF-8\r\n");
}
