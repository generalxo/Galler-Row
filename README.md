# Gallery Row

Gallery Row is a demo of a **multi-tenant SaaS art store**: one platform where many artists or galleries each run their own online shop for selling artwork.

It is a portfolio/demo project, not a production service.

## The idea

Think of a street lined with galleries — each with its own storefront, but all sharing the same street. Gallery Row is the street.

- **Tenants** are artists or galleries. Each tenant gets its own store with its own artwork, branding and orders.
- **Tenant data is isolated** — one store never sees another store's artwork, customers or orders.
- **Visitors** browse a store's collection and buy artwork from it.
- **The platform** (Gallery Row itself) handles sign-up of new stores and the shared infrastructure every store runs on.

## Current status

Early stage. The project has been stripped back to a clean base:

- Login and logout for a single user type (no registration yet)
- A public home page at `/` with the Gallery Row banner

Multi-tenancy, stores, artwork and checkout are not built yet.

## Tech stack

- [Laravel 13](https://laravel.com) (PHP 8.3+)
- [Livewire 4](https://livewire.laravel.com) — class-based components: a PHP class in `app/Livewire`, with its Blade view in `resources/views/livewire`
- [Laravel Fortify](https://laravel.com/docs/fortify) for authentication
- [Tailwind CSS 4](https://tailwindcss.com) built with Vite
- MySQL

## Getting started

Requirements: PHP 8.3+, Composer, Node.js and a running MySQL server.

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate
```

Edit the `DB_*` values in `.env` to match your MySQL server, create the database, then run:

```bash
php artisan migrate --seed
composer dev
```

The app runs at http://localhost:8000. The seeder creates a test user:

- Email: `test@example.com`
- Password: `password`

> After changing `.env`, restart `composer dev` — the running server keeps the values it started with.

## Tests

```bash
composer test
```

Runs code style checks (Pint), static analysis (PHPStan) and the PHPUnit test suite. Tests use an in-memory SQLite database, so they never touch your MySQL data.

## Fonts

- **Poppins** — default font for all text
- **Limelight** — display font for headings and the banner (`font-display` class)

Both are Google Fonts, downloaded at build time and served from the app itself (configured in `vite.config.js`), so pages make no requests to Google.
