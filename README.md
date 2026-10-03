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

- Login and logout (no registration yet)
- A public home page at `/` with the Gallery Row banner
- The core data model: stores, roles, artwork, orders (see below)

Store routing, storefront pages and checkout are not built yet.

## Data model

All tenants share one database. Tenant tables carry a `store_id`, and the `BelongsToStore` trait (`app/Models/Concerns`) scopes queries to the active store held in `App\Support\CurrentStore`. With no active store set (admin, console, seeders), every row is visible.

| Model | Purpose |
|---|---|
| `User` | Platform account. Anyone can shop. `is_admin` marks a platform admin. |
| `Store` | The tenant: one artist's or gallery's shop. |
| `StoreMembership` | `store_user` pivot holding a user's role in a store: `owner` or `staff`. |
| `Artist` | An artist whose work a store sells. |
| `Collection` | A curated group of artworks within a store. |
| `Artwork` | The shared columns of every piece: title, price, stock, status. |
| `PaintingDetail`, `PrintDetail`, `PhotographDetail`, `SculptureDetail`, `DigitalDetail` | Type-specific columns, linked by the polymorphic `artworkable` relation. |
| `ArtworkVariant` | Purchasable options of an edition, such as size or framing. |
| `Image` | Polymorphic images for artworks, stores and artists. |
| `Order`, `OrderItem` | A purchase from one store. Items keep a snapshot of the title and price. |

Inventory rules:

- `is_original` is set explicitly. It is never worked out from stock, so an edition with one print left is still an edition.
- An original has at most 1 in stock and no `edition_size`.
- An edition must have an `edition_size`, and its stock can't be larger than that.
- Saving an artwork that breaks these rules throws `InvalidArtworkInventory`.

Money is stored as whole cents in the store's `currency`. Polymorphic columns store short keys such as `painting` or `artwork` (see `AppServiceProvider`).

## Tech stack

- [Laravel 13](https://laravel.com) (PHP 8.4+)
- [Livewire 4](https://livewire.laravel.com) — class-based components: a PHP class in `app/Livewire`, with its Blade view in `resources/views/livewire`
- [Laravel Fortify](https://laravel.com/docs/fortify) for authentication
- [Tailwind CSS 4](https://tailwindcss.com) built with Vite
- MySQL

## Getting started

Requirements: PHP 8.4+, Composer, Node.js and a running MySQL server.

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

The app runs at http://localhost:8000. The seeder creates these users, all with the password `password`:

- `test@example.com`: a customer with one order
- `admin@example.com`: platform admin
- `owner@northlight.test`, `owner@vermeer.test`, `owner@claykiln.test`: owners of the three demo stores. Each store also gets artists, collections and artwork of every type.

> After changing `.env`, restart `composer dev` — the running server keeps the values it started with.

## Tests

```bash
composer test
```

Runs code style checks (Pint), static analysis (PHPStan) and the PHPUnit test suite. Tests use an in-memory SQLite database, so they never touch your MySQL data.

## Fonts

- **Playfair Display** — default font for all text (`font-editorial`, `type-heading-*`, `type-body*`)
- **Lobster Two** — display font for banners and headlines (`font-display`, `type-headline-*`)

Both are Google Fonts, downloaded at build time and served from the app itself (configured in `vite.config.js`), so pages make no requests to Google. See `docs/design-system.md` for the full Gallery Row design system.
