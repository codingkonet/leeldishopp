# LebeldiShop — PHP + MySQL

Plain PHP (PDO) + MySQL port of the LebeldiShop storefront: catalog, cart, COD checkout,
customer accounts, and a basic admin panel (orders + products).

## Requirements

- PHP 8.1+ with the `pdo_mysql` extension
- MySQL 8+ (or MariaDB 10.4+)
- Apache with `mod_rewrite`/`.htaccess` support, or PHP's built-in server for local dev

## Quick installer

The easiest method is the browser installer:

1. Copy this folder to your web server.
2. Make sure PHP has `pdo_mysql` enabled.
3. Start the local server with `php -S localhost:8000` or open the folder in Apache.
4. Visit `http://localhost:8000/setup.php`.
5. Enter your MySQL credentials and choose the SUPER_ADMIN email/password.
6. When installation finishes, delete `setup.php` from the server.

The installer creates the database, imports `database/schema.sql`, creates the SUPER_ADMIN,
writes `.env`, and creates an `.installed` lock file. Never leave `setup.php` publicly accessible.

For Windows with PHP installed, you can also double-click `install.bat`, or run:

```bash
php install.php
```

The root `.htaccess` blocks `.env`, `setup.php`, and `install.php` from public access on Apache.

## Import plugins and themes

After installation, sign in as an administrator and open:

```text
/admin/extensions.php
```

Upload a plugin or theme as a ZIP file. ZIP files are limited to 10 MB, unsafe archive paths
are rejected, and imported code is not executed automatically. Imported plugins are stored in
`plugins/`; imported themes are stored in `themes/`. Use the **Activer** button to select the
active extension. Only install ZIP files from sources you trust, because PHP code inside an
extension can run if the application explicitly loads it.

## Manual installation

### 1. Configure environment

Copy `.env.example` to `.env` and set your database credentials, then export them (or set them
in your web server's environment — PHP does not auto-load `.env` files):

```bash
copy .env.example .env
```

On Apache, set these in your vhost or `.htaccess` with `SetEnv`, e.g.:

```apache
SetEnv DB_HOST 127.0.0.1
SetEnv DB_NAME lebeldishop
SetEnv DB_USER root
SetEnv DB_PASS your-password
```

For local testing with PHP's built-in server, export variables in your shell before starting it.

### 2. Import the database

```bash
mysql -u root -p -e "CREATE DATABASE lebeldishop CHARACTER SET utf8mb4"
mysql -u root -p lebeldishop < database/schema.sql
```

This creates all tables and seeds demo categories, products, and services.

### 3. Create the SUPER_ADMIN account

Never hardcode admin credentials. Run the CLI script and enter the email/password interactively:

```bash
php database/create_admin.php
```

### 4. Run the app

```bash
php -S localhost:8000
```

Then visit:

- Storefront: http://localhost:8000/
- Admin panel: http://localhost:8000/admin/index.php (log in via `/account/login.php` first)

For production, point your Apache/Nginx document root at this folder and ensure `config/`,
`database/`, and `includes/` are not web-accessible (the included `.htaccess` files already
deny direct access to them under Apache).

## Local Docker test

With Docker Desktop installed:

```bash
docker compose up --build
```

Open `http://localhost:8080/setup.php`, enter the Docker database values shown in
`docker-compose.yml`, and create your admin account. The schema is imported automatically on
the first database startup. To reset the local database completely, run `docker compose down -v`.

## Security notes

- Passwords are hashed with `password_hash()` (bcrypt).
- All queries use PDO prepared statements.
- CSRF tokens are required on every state-changing form.
- Failed logins lock the account for 15 minutes after 5 attempts.
- Admin routes are protected server-side via `require_admin()`.
- No secrets are committed; all credentials come from environment variables.

## Structure

```
lebeldishop-php/
├── config/         # DB + app config (not web-accessible)
├── database/        # schema.sql + CLI admin-creation script
├── includes/        # shared PHP: auth, cart, functions, header/footer, lang
├── account/          # customer login/register/orders/profile
├── admin/            # protected admin dashboard, orders, products
├── assets/css/       # stylesheet
├── index.php, products.php, product.php, services.php, cart.php, checkout.php, order-confirmation.php
```
