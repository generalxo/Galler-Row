# Storefront

What visitors see on a store's host. The pages are class-based Livewire components in `app/Livewire/Storefront`, with views in `resources/views/livewire/storefront`, using the `layouts/store.blade.php` layout.

## Pages

| URL | Component | Shows |
| --- | --- | --- |
| `/` | `Home` | store name and description, the featured collection, recently added work, artists |
| `/artworks` | `Artworks\Index` | every artwork on display, 12 per page |
| `/artworks/{slug}` | `Artworks\Show` | one artwork with its wall label |
| `/collections` | `Collections\Index` | collections in order, with a preview of three works |
| `/collections/{slug}` | `Collections\Show` | a collection's works in their set order |
| `/artists` | `Artists\Index` | artists with work on display |
| `/artists/{slug}` | `Artists\Show` | an artist's work, newest first |

All of them return a 404 on the root domain and for another store's records.

## What's on display

Published and sold work (`Artwork::onDisplay()`). Drafts and archived work never appear, and their detail pages are 404s. Sold work stays visible, marked "Sold" with no price.

## Artworks list

Filters live in the URL (`?type=print&kind=editions&available=1&sort=price-asc`), so a filtered list can be shared:

- **Type:** only the types the store actually sells are offered.
- **Originals or editions.**
- **Available only:** hides work with no stock.
- **Sort:** newest (default), price low to high, price high to low.

## Artwork detail

The image sits beside a "wall label" panel, styled after the labels galleries pin beside a work. It shows:

- artist (linking to their page), title, year
- type-specific facts: medium and support for paintings, technique for prints, material for sculpture, format and licence for digital work
- size, price in the store's currency
- availability: "Original, one of one", "14 of 25 left", "Last one left of an edition of 25", or "Sold"
- options such as framing, with price and stock
- the collections it belongs to

There's no buy button yet; it comes with the cart.

## Store accent colour

Each store can set `accent_color` and `on_accent_color` (`#rrggbb`). The store layout puts them on `<body>` as `--color-accent` / `--color-on-accent`, so every `bg-accent`, `border-accent` and `text-on-accent` inside picks them up:

- the header's bottom border
- the active nav link underline
- the "New" badge
- accent buttons
- the wall label's edge

`Store::accentStyle()` only outputs the variables when both values are plain hex colours. Anything else leaves Gallery Row's default Ember Orange in place.

The demo stores use sea blue (`#1f4e6e`), ultramarine (`#2b3f8f`) and moss green (`#4a5d32`), all on parchment text. Each reaches at least 4.2:1 against the bone-cream page and 5.4:1 against parchment. When store settings are built, validate new accents for at least 3:1 against the page and against the on-accent colour (see [design-system.md](design-system.md)). `App\Support\Color::contrast()` does the maths.

## Shared pieces

- `<x-artwork-card>`: card with image, title, artist, price or "Sold", and a "New" badge for work published in the last 14 days.
- `<x-button>`: see [design-system.md](design-system.md#button-x-button).
- `components/pagination.blade.php`: Previous / Next pagination, because Livewire's default view uses Tailwind classes the design system removes.
