# Onje Casa POS

Laravel-based POS system with staff OTP login, admin OTP, cashier shifts, product import, receipts, and dashboard controls.

## Setup

1. `composer install`
2. Configure `.env` database settings (defaults target XAMPP MySQL).
3. Create database `rposystem` in MySQL.
4. Run:
   - `php artisan key:generate`
   - `php artisan migrate --seed`
   - `php artisan serve`

## App Routes

- `/` Staff OTP login
- `/admin` Admin login
- `/dashboard` Admin dashboard
- `/cashier/dashboard` Cashier dashboard

## Hostinger Deployment

1. Set the site PHP version to 8.3 or newer.
2. Upload or pull the repository into the hosting account.
3. Copy `.env.production.example` to `.env` on the server and fill in real Hostinger database, domain, mail, and API credentials.
4. Generate a production app key if `APP_KEY` is empty:
   ```bash
   php artisan key:generate
   ```
5. Install optimized PHP dependencies:
   ```bash
   composer install --no-dev --optimize-autoloader
   ```
6. Run database migrations:
   ```bash
   php artisan migrate --force
   ```
7. Cache production config and routes:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
8. Make sure `storage/` and `bootstrap/cache/` are writable by the web server.
9. Configure SMTP before using OTP login in production. `MAIL_MAILER=log` only writes OTP emails to logs.

The root `.htaccess` rewrites requests into `public/`, so the Laravel app can run even when Hostinger points the domain at the project root.
