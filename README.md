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

Early stage, built in phases (see the [roadmap](docs/roadmap.md)). Working now:

- **Platform site** on the root domain: a marketing page, and an "Open your store" form that saves store applications
- **Store routing:** every store on its own subdomain or custom domain, with tenant data kept apart ([tenancy](docs/tenancy.md))
- **Public storefronts:** artworks with filters, a detail page per artwork, collections and artists, each store in its own accent colour ([storefront](docs/storefront.md))
- **Demo catalogue:** 74 real public-domain artworks from the Art Institute of Chicago ([artwork catalogue](docs/artwork-catalogue.md))
- **Base components**, starting with `<x-button>` ([design system](docs/design-system.md))
- **Login and logout** (no registration yet)

The store back office, cart and checkout are not built yet.

## Documentation

| Page | What it covers |
| --- | --- |
| [Roadmap](docs/roadmap.md) | What's done and what comes next |
| [Tenancy and store routing](docs/tenancy.md) | How a host maps to a store, scoping, sessions, custom domains, platform vs storefront vs back office |
| [Data model](docs/data-model.md) | Models, artwork status, inventory rules, money |
| [Storefront](docs/storefront.md) | Store pages, filters, the artwork detail page, store accent colours |
| [Artwork catalogue](docs/artwork-catalogue.md) | Where the demo art comes from, how it was picked and downloaded, licence, how it's seeded |
| [Design system](docs/design-system.md) | Colours, type, spacing, the `<x-button>` component, design rules |
| [Local domains](docs/local-domains.md) | Every local address and demo login |

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
php artisan storage:link
php artisan migrate --seed
composer dev
```

`storage:link` makes the seeded artwork images (on the `public` disk) reachable under `/storage`.

The platform runs at http://gallery-row.test:8000 and the demo stores at http://northlight.gallery-row.test:8000, http://studio-vermeer.gallery-row.test:8000 and http://clay-and-kiln.gallery-row.test:8000 (Clay & Kiln also at its custom domain, http://clayandkiln.test:8000). [docs/local-domains.md](docs/local-domains.md) lists every address and login. The seeder creates these users, all with the password `password`:

- `test@example.com`: a customer with one order
- `admin@example.com`: platform admin
- `owner@northlight.test`, `owner@vermeer.test`, `owner@claykiln.test`: owners of the three demo stores. Each store's catalogue comes from the [artwork catalogue](docs/artwork-catalogue.md).

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

- **Playfair Display**: default font for all text (`font-editorial`, `type-heading-*`, `type-body*`)
- **Lobster Two**: display font for banners and headlines (`font-display`, `type-headline-*`)

Both are Google Fonts, downloaded at build time and served from the app itself (configured in `vite.config.js`), so pages make no requests to Google.

## Credits

Artwork images and data: [Art Institute of Chicago](https://www.artic.edu/), released under [CC0](https://creativecommons.org/publicdomain/zero/1.0/). Prices and stock are made up, nothing is for sale, and Gallery Row isn't affiliated with or endorsed by the museum. Details in [docs/artwork-catalogue.md](docs/artwork-catalogue.md).
