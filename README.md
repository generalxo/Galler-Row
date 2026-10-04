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
- A public home page on the root domain with the Gallery Row banner
- Store routing: every store on its own subdomain or custom domain (see below), with a placeholder storefront page
- The core data model: stores, roles, artwork, orders (see below)

Storefront catalogue pages and checkout are not built yet.

## Store routing

Each store is served on its own host. `App\Http\Middleware\IdentifyStore` runs on every web request (including Livewire updates) and sets `CurrentStore` from the host:

| Host | Serves |
|---|---|
| `gallery-row.com` (`TENANCY_ROOT_DOMAIN`) | the platform site, no store |
| `www.gallery-row.com` | 301 to the root domain |
| `{slug}.gallery-row.com` | the store with that `slug` |
| any other host | the store with that custom `domain` |

Unknown hosts, reserved subdomains (`config/tenancy.php`) and stores that aren't `active` get a 404. Sessions are per host, so logging in on one store doesn't log you in on another. Outside a request (mail, jobs), build store links with `$store->url('/path')`.

## Data model

All tenants share one database. Tenant tables carry a `store_id`, and the `BelongsToStore` trait (`app/Models/Concerns`) scopes queries to the active store held in `App\Support\CurrentStore`. With no active store set (admin, console, seeders), every row is visible.

| Model | Purpose |
|---|---|
| `User` | Platform account. Anyone can shop. `is_admin` marks a platform admin. |
| `Store` | The tenant: one artist's or gallery's shop. Served at `{slug}.` the root domain, or at its optional custom `domain`. |
| `StoreApplication` | A request to open a store, sent from the "Open your store" form on the platform site. Not tenant data. `pending` until a platform admin approves or rejects it. |
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
- [Alpine.js](https://alpinejs.dev) for small client-side interactions. It ships inside Livewire, so it isn't an npm dependency. Every layout loads it with `@livewireStyles` / `@livewireScripts`, including plain Blade pages like login.
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

Edit the `DB_*` values in `.env` to match your MySQL server and create the database.

Stores run on subdomains of `gallery-row.test`, so add the demo hosts to your hosts file first (see [Editing the hosts file](#editing-the-hosts-file) below). Then run:

```bash
php artisan migrate --seed
composer dev
```

The platform runs at http://gallery-row.test:8000 and the demo stores at http://northlight.gallery-row.test:8000, http://studio-vermeer.gallery-row.test:8000 and http://clay-and-kiln.gallery-row.test:8000 (Clay & Kiln also at its custom domain, http://clayandkiln.test:8000). [docs/local-domains.md](docs/local-domains.md) lists every address and login. The seeder creates these users, all with the password `password`:

- `test@example.com`: a customer with one order
- `admin@example.com`: platform admin
- `owner@northlight.test`, `owner@vermeer.test`, `owner@claykiln.test`: owners of the three demo stores. Each store also gets artists, collections and artwork of every type.

> After changing `.env`, restart `composer dev` — the running server keeps the values it started with.

### Editing the hosts file

`gallery-row.test` isn't a real domain, so your computer has to be told it points at your own machine. Your operating system's hosts file does this. Add this line to it:

```
127.0.0.1 gallery-row.test www.gallery-row.test northlight.gallery-row.test studio-vermeer.gallery-row.test clay-and-kiln.gallery-row.test clayandkiln.test
```

**Windows** (also when the app runs in WSL and you browse from Windows):

1. Open the Start menu, type `Notepad`, right-click it and choose **Run as administrator**. The hosts file can only be saved with admin rights.
2. In Notepad choose **File → Open**, paste `C:\Windows\System32\drivers\etc\hosts` into the file name box and open it. If the file doesn't show up in the dialog, set the file type filter to **All files**.
3. Add the line above at the end of the file and save.
4. Open a Command Prompt and run `ipconfig /flushdns` so Windows forgets old lookups.

**WSL**: WSL rebuilds its own `/etc/hosts` from the Windows file each time it starts, so after editing the Windows file, run `wsl --shutdown` in PowerShell and reopen your WSL terminal. Commands inside WSL, such as `curl` and headless Chromium, then resolve the same hosts. Don't edit `/etc/hosts` inside WSL directly; it's overwritten on the next restart unless `generateHosts = false` is set under `[network]` in `/etc/wsl.conf`.

**macOS / Linux**: run `sudo nano /etc/hosts`, add the line at the end and save (Ctrl+O, Enter, Ctrl+X). On macOS, run `sudo dscacheutil -flushcache; sudo killall -HUP mDNSResponder` afterwards.

To check it worked, run `ping northlight.gallery-row.test`: it should reply from `127.0.0.1`. Hosts files don't support wildcards, so every new store subdomain or custom domain has to be added to the line.

## Tests

```bash
composer test
```

Runs code style checks (Pint), static analysis (PHPStan) and the PHPUnit test suite. Tests use an in-memory SQLite database, so they never touch your MySQL data.

## Fonts

- **Playfair Display** — default font for all text (`font-editorial`, `type-heading-*`, `type-body*`)
- **Lobster Two** — display font for banners and headlines (`font-display`, `type-headline-*`)

Both are Google Fonts, downloaded at build time and served from the app itself (configured in `vite.config.js`), so pages make no requests to Google. See `docs/design-system.md` for the full Gallery Row design system.
