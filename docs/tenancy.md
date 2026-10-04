# Tenancy and store routing

Every store is a tenant. All tenants share one database and one codebase, and each store is served on its own host.

## Three areas

| Area | Host | Who | What |
| --- | --- | --- | --- |
| Platform | root domain (`gallery-row.test` locally, `gallery-row.com` in production) | visitors, platform admin | marketing page, "Open your store" application form; later `/admin` to approve applications and suspend stores |
| Storefront | the store's own host, `/` | visitors, customers | artworks, collections, artists; later cart, checkout, customer account |
| Back office (not built yet) | the store's own host, `/manage` | store owner and staff | owner: settings, branding, domain, team. Owner and staff: artworks, artists, collections, orders |

The back office lives on the store's host so that one login covers one store, and every query there is scoped to that store automatically.

## How a request finds its store

`App\Http\Middleware\IdentifyStore` is prepended to the `web` middleware group, so it runs on every web request: pages, Fortify login, and Livewire's `/livewire/update` calls. It reads the host:

| Host | Result |
| --- | --- |
| `gallery-row.test` (`TENANCY_ROOT_DOMAIN`) | platform, no current store |
| `www.gallery-row.test` | 301 redirect to the root domain |
| `{slug}.gallery-row.test` | the store with that `slug` |
| any other host | the store with that custom `domain` |
| no match, a reserved subdomain, or a store that isn't `active` | 404 |

- Reserved subdomains (`www`, `admin`, `app`, `api`, `mail`, `static`) live in `config/tenancy.php` and never resolve to a store.
- The lookup is `Store::findForHost()`.
- `www.` only redirects for paths that exist; an unknown path gives the router's 404 first.

Routes in `routes/web.php`:

- Platform routes sit in a `Route::domain(config('tenancy.root_domain'))` group.
- Storefront routes have no domain constraint, so custom domains match too. Their `EnsureStore` middleware returns a 404 when no store is set, so they don't work on the root domain.
- Fortify's login routes work on every host.

## Scoping

- `App\Support\CurrentStore` holds the store for the current request (a scoped singleton, reset between requests and jobs).
- Tenant models (`Artist`, `Collection`, `Artwork`, `Order` and others) use the `BelongsToStore` trait. While a store is set, their queries only see that store's rows, and new rows get its `store_id`.
- `IdentifyStore` runs before route-model binding, so a URL like `/artworks/{slug}` can only find the current store's artwork. Another store's slug is a 404.
- With **no** store set (root domain, console, seeders, queue jobs), scoping is off and every store's rows are visible. Platform pages that show tenant data must filter it themselves.
- Storefront views get the store as `$currentStore` from a view composer in `AppServiceProvider`.

## Sessions

Session cookies are host-only (`SESSION_DOMAIN=null`). Logging in on one store doesn't log you in on another, or on the platform. Custom domains work the same way.

## Links to a store

Inside a store request, `route()` builds URLs on the current host. Outside a request (mail, queued jobs, the platform site), use `$store->url('/path')`: it uses the custom domain if set, otherwise `{slug}.{root domain}`, with the scheme and port from `APP_URL`.

## Custom domains

A store's optional `domain` column (unique) makes it reachable on that host as well as its subdomain. In production the store owner points their domain's DNS at the server. Store subdomains need no DNS work per store: one wildcard record (`*.gallery-row.com`) covers them all.

## Adding a store locally

1. Create the store (seeder, tinker, or later the platform admin) with status `active` and a `slug`.
2. Add `{slug}.gallery-row.test` (and its custom domain, if any) to your hosts file. Hosts files don't support wildcards. See [Editing the hosts file](../README.md#editing-the-hosts-file).
