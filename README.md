# Raffle Desk

Raffle Desk is a small-shop raffle manager built with Laravel 13, Inertia, Vue 3, and Tailwind CSS. It provides a public 00–99 number grid and an authenticated owner workspace for manually recording sales and drawing a winner.

## Features

- One raffle with 100 numbered tickets, each available, reserved, or sold.
- Public availability grid that never exposes buyer names or phone numbers.
- Owner login, buyer details, and manual sale recording. There are no payment integrations.
- A one-time random draw among sold tickets, with the winning ticket, draw seed, and timestamp stored in the database.
- A database seeder with 40 sample sales for a ready-to-view demo.

## Requirements

- PHP 8.3 or newer and Composer
- Node.js 22 or newer and npm
- MySQL 8 or MariaDB for a MySQL-backed installation. SQLite is also supported for local development and automated tests.

## Installation

1. Install PHP dependencies and create the local environment file:

   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   ```

   In PowerShell, use `Copy-Item .env.example .env` in place of `cp`.

2. Create the MySQL database named `raffle_desk`, then set `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` in `.env`.

3. Install frontend dependencies, create the schema, and load the demo raffle:

   ```bash
   npm install
   php artisan migrate --seed
   npm run build
   ```

4. Create the owner account in Tinker. Choose a unique email and a strong password:

   ```bash
   php artisan tinker
   ```

   ```php
   App\Models\User::create([
       'name' => 'Raffle owner',
       'email' => 'owner@example.com',
       'password' => 'replace-with-a-strong-password',
   ]);
   ```

5. Start the Laravel server:

   ```bash
   php artisan serve
   ```

   Open the URL printed by Artisan. The public raffle is available at `/`; the owner signs in at `/login`.

For local SQLite development, leave `DB_CONNECTION=sqlite` in `.env` and run `php artisan migrate --seed`.

## Reset the raffle

To clear all sales and the draw while keeping the same 100 ticket records, open Tinker and run:

```php
$raffle = App\Models\Raffle::firstOrFail();
$raffle->numbers()->update([
    'status' => App\Models\RaffleNumber::STATUS_AVAILABLE,
    'buyer_name' => null,
    'buyer_phone' => null,
]);
$raffle->forceFill([
    'winner_number_id' => null,
    'draw_seed' => null,
    'drawn_at' => null,
])->save();
```

Running `php artisan db:seed` restores the screenshot-ready demo state with 40 sample tickets marked sold.

## Tests and formatting

```bash
php artisan test
vendor/bin/pint --test
npm run build
```

The feature suite includes Pest coverage for selling tickets, rejecting duplicate sales, drawing only among sold tickets, and rejecting a second draw.
