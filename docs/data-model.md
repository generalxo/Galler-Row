# Data model

All tenants share one database. Tenant tables carry a `store_id` and are scoped to the current store (see [tenancy.md](tenancy.md)).

| Model | Purpose |
| --- | --- |
| `User` | Platform account. Anyone can shop. `is_admin` marks a platform admin. |
| `Store` | The tenant: one artist's or gallery's shop. Served at `{slug}.` the root domain, or at its optional custom `domain`. Has an optional `accent_color` / `on_accent_color` for its storefront. |
| `StoreApplication` | A request to open a store, sent from the "Open your store" form. Not tenant data. `pending` until a platform admin approves or rejects it. |
| `StoreMembership` | `store_user` pivot holding a user's role in a store: `owner` or `staff`. |
| `Artist` | An artist whose work a store sells. |
| `Collection` | A curated group of artworks within a store, ordered by `position`. One can be featured. |
| `Artwork` | The shared columns of every piece: title, price, stock, status, dimensions. |
| `PaintingDetail`, `PrintDetail`, `PhotographDetail`, `SculptureDetail`, `DigitalDetail` | Type-specific columns, linked by the polymorphic `artworkable` relation. `ArtworkType` maps each type to its model. |
| `ArtworkVariant` | Purchasable options of an edition, such as framing. |
| `Image` | Polymorphic images for artworks, stores and artists. Files live on the `public` disk; `$image->url()` gives a URL on the current host. |
| `Order`, `OrderItem` | A purchase from one store. Items keep a snapshot of the title and price. |

## Artwork status

`draft`, `published`, `sold` or `archived` (`ArtworkStatus`).

- Storefronts show `published` and `sold` work (`Artwork::onDisplay()`), never drafts or archived work.
- `$artwork->isSoldOut()` is true when the status is `sold` or no stock is left. Cards and detail pages use it to show "Sold".

## Inventory rules

- `is_original` is set explicitly. It is never worked out from stock, so an edition with one print left is still an edition.
- An original has at most 1 in stock and no `edition_size`.
- An edition must have an `edition_size`, and its stock can't be larger than that.
- Saving an artwork that breaks these rules throws `InvalidArtworkInventory`.

## Money

Stored as whole cents in the store's `currency` (default EUR). Display with `App\Support\Money::format($cents, $currency)`.

## Polymorphic keys

The morph map is enforced (`AppServiceProvider::configureMorphMap`), so polymorphic columns store short keys such as `painting` or `artwork`. Register every new polymorphic model there, or saving the relation throws.
