# Group 167 Final Project (Laravel Migration)

This project has been converted from raw PHP to Laravel.

## Setup

1. `composer install`
2. Configure `.env` database settings (defaults target XAMPP MySQL).
3. Create database `rposystem` in MySQL.
4. Import schema/data:
   - `database/schema/rposystem.sql`
5. Run:
   - `php artisan key:generate`
   - `php artisan serve`

## App Routes

- `/` Login (admin or cashier)
- `/dashboard`
- `/products`
- `/customers`
- `/orders`
- `/payments`

## Legacy Removal

Legacy standalone PHP files were removed from the repository root and replaced by Laravel structure.
