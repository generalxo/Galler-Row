# Gallery Row design system

The tokens and fonts for Gallery Row. The full brand book (guidelines, colour roles, contrast notes) lives in the Gallery Row design system artifact; this is how it's wired into the app.

Requires Tailwind CSS v4.

## Files

| File | What |
| --- | --- |
| `resources/css/gallery-row.css` | Tailwind `@theme`: colours, spacing, radii, fonts, type scale, `type-*` utilities, base styles |
| `vite.config.js` | `fonts:` option of `laravel-vite-plugin`: Lobster Two and Playfair Display, downloaded at build time and self-hosted |

## Setup

1. `resources/css/app.css`:

   ```css
   @import 'tailwindcss';
   @import './gallery-row.css';

   @source '../views';
   ```

2. Fonts are registered in `vite.config.js` and emitted by the `@fonts` directive, which sits before `@vite` in `resources/views/partials/head.blade.php` (included by every layout):

   ```blade
   @fonts

   @vite(['resources/css/app.css', 'resources/js/app.js'])
   ```

## Using the tokens

Tailwind's default colours, radii, text sizes, shadows and blurs are removed, so only Gallery Row values exist. `bg-white`, `text-gray-500`, `rounded-lg`, `shadow-md`, `drop-shadow-md` and `blur-sm` don't compile.

**Colour:** the page is `bone-cream` (set on `<html>` in the base styles, so layouts need no background class). Raised surfaces (cards, inputs, panels, popovers) are `bg-parchment`. Also `text-ink-black`, `text-charcoal` (muted text 24px+ only; 3.5:1 on the page), `border-pure-black`, `bg-accent`, `text-on-accent`.

**Feedback:** `text-success` / `bg-success-bg` / `border-success`, and the same for `warning`, `error` and `info`. Use them for alerts, flash messages, toasts and form validation, always with a text label or icon. An invalid field gets `border-error` and `text-error` helper text, with the label left in `text-ink-black`. An alert is `bg-{role}-bg border border-{role}` with ink-black body text and the title or icon in the role colour. Success text is only 4.3:1 on the bone-cream page, so don't put `text-success` straight on the page: use the alert treatment or a parchment surface. Error, warning and info text pass 4.5:1 on the page, so inline helper text like `text-error` is fine.

**Order status:** `bg-status-paid-bg text-status-paid` (also `shipped`, `delayed`, `damaged`). These are aliases of success, info, warning and error.

**Spacing:** 4px base. `p-4` = 16px, `gap-6` = 24px, `py-12` = 48px. Stick to the scale: 0, 1, 2, 3, 4, 5, 6, 8, 10, 12, 16, 20, 24.

**Shadow:** one token, `shadow-card`: offset `8px 8px`, blur `16px`, ink-black at 30% opacity, falling down and to the right. Artwork cards only. Blur is allowed inside this shadow; there are no other shadows or drop shadows, and no blur filters (`blur-*`, `backdrop-blur-*`).

**Radius:** `rounded-none` (default for cards and images), `rounded-xs` 4px, `rounded-sm` 8px, `rounded-md` 12px (the maximum), `rounded-full` (avatars only).

**Type:** `type-*` sets family, size, line height and tracking in one class.

| Class | Font | Use |
| --- | --- | --- |
| `type-headline-533` … `type-headline-17` | Lobster Two | Banners, hero headlines, section titles |
| `type-statement` | Playfair Display | Large serif statement |
| `type-heading-1` … `type-heading-5` | Playfair Display | Headings inside a section |
| `type-lead`, `type-body-lg`, `type-body`, `type-body-sm` | Playfair Display | Running text, nav, captions |

`text-headline-212`, `text-body` and so on also exist if you want the size, line height and tracking without the family. Pair them with `font-display` or `font-editorial`.

## Store branding (multi-tenancy)

A store can change only its accent colour (plus logo and name). Components use `bg-accent` / `text-accent` / `text-on-accent`, never `ember-orange` directly, so the store colour flows through.

Override the two CSS variables on the storefront's wrapper element; everything inside picks them up:

```blade
<div style="--color-accent: {{ $store->accent_color }}; --color-on-accent: {{ $store->on_accent_color }}">
    {{-- storefront --}}
</div>
```

Only output values that match `#rrggbb` (validate on save and when rendering); otherwise leave the variables unset so Ember Orange on Parchment applies. Validate a store's accent on save: it should reach at least 3:1 against the bone-cream page (`#cdc6be`) and against its on-accent colour. Ember Orange itself is 3.1:1 on bone-cream, so it only just passes.

## Components

Anonymous Blade components in `resources/views/components/`, using only the tokens above.

### Button (`<x-button>`)

Use it for every button and every link styled as a button. Plain text links stay `<a>`.

```blade
<x-button>Save</x-button>                                    {{-- <button type="button">, md, ink, solid --}}
<x-button type="submit" class="w-full">Log in</x-button>
<x-button :href="route('stores.create')">Open your store</x-button>  {{-- renders <a> --}}
<x-button color="accent">Browse all artworks</x-button>
<x-button color="#1f4e6e">Custom colour</x-button>
<x-button variant="ghost" size="sm">Log out</x-button>
```

| Prop | Values |
| --- | --- |
| `size` | `sm` (17px, `px-3 py-1.5`), `md` default (17px, `px-5 py-3`), `lg` (`type-lead`, `px-7 py-4`) |
| `color` | `ink` default, `parchment` (for dark surfaces), `accent` (store colour), `danger` (destructive actions), or a `#rrggbb` hex |
| `variant` | `solid` default, `outline` (border in the colour), `ghost` (text only, parchment on hover) |
| `text-color` | `#rrggbb`, custom hex only. Without it the text is ink-black or parchment, whichever contrasts more with the background. |
| `href` | Renders an `<a>` instead of a `<button>` |

Every other attribute (`wire:*`, `data-test`, `class`, `disabled`) is passed through.

- Solid `accent` buttons use `type-body-lg font-semibold` at `sm` and `md`, because on-accent text on Ember Orange is only 4.0:1.
- `outline` and `ghost` always use ink-black text, so they stay readable on bone-cream and parchment whatever the colour.
- A custom colour that isn't a plain `#rrggbb` value falls back to `ink`, so nothing else reaches the `style` attribute. `App\Support\Color` holds the hex check and contrast maths.

### Not built yet

Build these when they're needed:

- **NEW badge:** `bg-accent text-on-accent rounded-xs`, label `type-body-lg font-semibold` (on-accent text on Ember Orange is only 4.0:1, so it must be 19px semibold or larger).
- **Order status tag:** `bg-status-{status}-bg text-status-{status}` with `rounded-xs`, always with a text label. Statuses: paid, shipped, delayed, damaged; awaiting payment uses `bg-parchment text-ink-black`. Write full class names (not interpolated) so Tailwind finds them.
- **Alert / flash message:** `bg-{role}-bg border border-{role}`, title `type-body font-semibold text-{role}`, body `type-body-sm text-ink-black`, `rounded-xs`. Roles: success, warning, error, info. Write full class names per role.
- **Banner:** Lobster Two `type-headline-*`, one or two words, stepping down a size on small screens.
- **Artwork card:** `bg-parchment shadow-card`, image bleeding to the edge with no radius, title `type-heading-5`, artist `type-body-sm italic`, optional NEW badge.

## Rules worth enforcing in review

- Status colours only on order tables, order details and the store admin, and always with a text label.
- No gradients, blur filters, or shadows. The one exception is `shadow-card` on artwork cards, which may be soft (blurred).
- Body text flush left; never centre more than two lines.
- Body, nav and small headings at weight 400; `font-semibold` / `font-bold` only for rare emphasis.
