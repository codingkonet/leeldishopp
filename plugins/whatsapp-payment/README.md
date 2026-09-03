# LebeldiShop WhatsApp Payment

This package describes the WhatsApp payment integration included in the LebeldiShop PHP app.

## Install

This feature is already integrated in the main application. For an existing installation:

1. Apply `database/migrations/005_whatsapp_payment.sql`.
2. Open `/admin/settings.php`.
3. Enter the shop WhatsApp number in international format, for example `+212600000000`.
4. Save the settings.
5. Customers can select WhatsApp at checkout.

The implementation files are included in the application root archive with their original paths.
This plugin package is an integration bundle, not an independently executable WordPress-style plugin.
Use official WhatsApp links and comply with WhatsApp and local commerce requirements.
