# AliExpress URL Importer

Open `/admin/import-aliexpress.php` from the admin dashboard and paste a public HTTPS
AliExpress product URL. The importer reads public metadata only and creates an unpublished
product draft with its source URL.

Review the product before publishing: supplier price, currency conversion, stock, images,
trademarks, descriptions, VAT, import fees, and marketplace permissions.

If the page blocks automated requests or does not expose complete metadata, use the official
AliExpress API or the CSV importer instead. This integration does not bypass CAPTCHA, login,
rate limits, or anti-bot controls.
