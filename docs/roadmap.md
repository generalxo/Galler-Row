# Roadmap

Where the project is and what comes next. See [tenancy.md](tenancy.md) for how the platform, storefront and back office fit together.

| Phase | Status | What |
| --- | --- | --- |
| Store routing | Done | Stores on subdomains and custom domains, per-host sessions, marketing homepage |
| 1. Store applications | Done | "Open your store" form at `/open-a-store` saves a `StoreApplication`; nothing is created or emailed yet |
| 2. Public storefront | Done | Artworks, detail pages, collections, artists, store accent colours, public-domain demo catalogue ([artwork-catalogue.md](artwork-catalogue.md)) |
| Base components | In progress | `<x-button>` done; more as needed |
| 3. Back office foundation | Next | `/manage` on the store host, owner/staff access rules, dashboard, store settings, branding, custom domain, team |
| 4. Catalogue management | Planned | Add and edit artworks (originals, editions, variants), image upload, artists, collections |
| 5. Cart and checkout | Planned | Per-store cart, fake payment, orders, stock reduced on purchase |
| 6. Customer accounts and orders | Planned | Registration, order history, favourites, order statuses in the back office |
| 7. Platform admin | Planned | `/admin` on the root domain: approve applications into stores with an owner, suspend stores |

Phase 7 only depends on phase 1, so it can move earlier.
