# Artwork catalogue

The demo stores sell real artworks: 74 public-domain works from the [Art Institute of Chicago](https://www.artic.edu/) (AIC), hand-picked to fit each store. This page covers where they come from, how they were chosen, how they're downloaded and how they become store listings.

## Source and licence

- **Source:** the [AIC public API](https://api.artic.edu/docs/) (`api.artic.edu`, no key needed) for data, and AIC's [IIIF image server](https://www.artic.edu/iiif/2) for images.
- **Why AIC:** its images of public-domain works are released under [CC0](https://creativecommons.org/publicdomain/zero/1.0/). Every record has an `is_public_domain` flag, the artist's name, and structured dimensions (`dimensions_detail`), so sizes don't need parsing from text.
- **What's used:** only CC0 data: title, artist, year, medium, dimensions and image. AIC's `description` field is licensed CC-BY, so it isn't used. Each listing's description is written by the seeder instead.
- **Credit:** every listing's description says it's a demo and that the original is in the collection of the Art Institute of Chicago, which released the image under CC0.
- **Not real products:** prices, stock, editions and frames are made up. Nothing is for sale. Gallery Row isn't affiliated with or endorsed by the Art Institute of Chicago.

## What's in each store

| Store | Collection | Type | Works | Artists include |
| --- | --- | --- | --- | --- |
| Northlight Gallery | Mountains and rivers | Painting | 8 | Claude Monet, Frederic Edwin Church, Gustave Courbet, Alfred Sisley |
| | Coastlines | Painting | 8 | Claude Monet, Gustave Courbet, Winslow Homer, Martin Johnson Heade |
| | Harbour etchings | Print | 6 | Edwin Edwards, Winslow Homer, Eugène Louis Boudin, Johan Barthold Jongkind |
| | Sea photographs | Photograph | 4 | Gustave Le Gray, Francis Bedford, James Robertson |
| Studio Vermeer | Still lifes | Painting | 9 | Henri Fantin-Latour, Adriaen van der Spelt, Claude Monet, Raphaelle Peale |
| | Quiet rooms | Painting | 7 | Édouard Manet, Jean Baptiste Camille Corot, Jacob Ochtervelt |
| | Prints | Print | 6 | Mary Cassatt, Édouard Jean Vuillard, Leonard Bramer, Cornelis Visscher |
| | Digital downloads | Digital | 2 | Vincent van Gogh, Martin Johnson Heade |
| Clay & Kiln | Vessels | Sculpture | 15 | Wedgwood Manufactory, Daniel Greatbatch, Emmanuel Fremiet, James Callowhill |
| | Small sculpture | Sculpture | 9 | Antoine Louis Barye, Rosa Bonheur, Jean-Joseph Carriès, Joseph-Charles Marin |

Totals: Northlight 26, Studio Vermeer 24, Clay & Kiln 24. The two digital downloads reuse images of Studio Vermeer paintings, so there are 72 image files.

Artist names come straight from AIC. Some pieces have no named maker (Staffordshire stoneware, for example) and show "Maker unknown". One painting is credited to "Dutch", the way AIC records it.

## How the works were chosen

1. For each store's theme (landscapes and seascapes, still lifes and interiors, ceramics and small sculpture), AIC was searched for public-domain works of the right type with an image.
2. Search results were too mixed to use as they are: a search for "sea" photographs also returns portraits, and "sculpture" returns busts and religious figures. So the works were picked by hand and listed by AIC id in `FetchArtworks::CATALOGUE` (`app/Console/Commands/FetchArtworks.php`), grouped into the collections above.
3. Clay & Kiln deliberately leaves out ancient, Indigenous and religious objects (such as Moche and Ancestral Pueblo ceramics, or a Buddha figure) that AIC returns for "vessel" and "sculpture". Selling them, even as a demo, would be in poor taste. The same goes for a 19th-century face jug.

## How the images are downloaded

```bash
php artisan artworks:fetch
```

- Fetches each store's records in one bulk request (`/api/v1/artworks?ids=…`).
- Skips any work that isn't flagged `is_public_domain` or has no image, with a warning.
- Downloads each image at 600px wide (`/iiif/2/{image_id}/full/600,/0/default.jpg`). AIC refuses image requests without a `User-Agent`, so the command sends one plus an `AIC-User-Agent` header, as AIC asks.
- Waits 250 ms between downloads (`--delay=` to change it) and skips images already on disk, so it can be re-run safely.
- Writes everything to `database/seeders/artworks/`: one folder of images per store, plus `manifest.json`.

The output (about 7.9 MB) is committed, so seeding works offline and gives the same catalogue on every machine. You only need to run the command when changing the selection.

Each `manifest.json` entry looks like this:

```json
{
    "store": "northlight",
    "collection": "Mountains and rivers",
    "type": "painting",
    "source_id": 81546,
    "title": "The Petite Creuse River",
    "artist": "Claude Monet",
    "year": 1889,
    "medium": "Oil on canvas",
    "width_cm": 93,
    "height_cm": 65,
    "depth_cm": null,
    "image": "northlight/aic-81546.jpg"
}
```

## How the catalogue is seeded

`php artisan migrate --seed` runs `ArtworkCatalogueSeeder` (`database/seeders/`) for each demo store:

- **Artists:** one per artist name in that store.
- **Collections:** one per group, in catalogue order. The first is featured on the store home page.
- **Artworks:** the type-specific details come from the medium. "Oil on canvas" becomes medium "Oil" on support "canvas"; for prints, photographs and sculpture the medium is the technique or material.
- **Originals and editions:**
  - Paintings and sculpture are originals.
  - Prints are editions of 25 or 50, and photographs editions of 30.
  - Digital downloads are editions of 100 with a personal-use licence.
- **Sold and last-one work:** about one original in eight is already sold and stays on show marked "Sold". About one edition in ten is down to its last copy.
- **Prices in euros:**

  | Type | Range |
  | --- | --- |
  | Painting | €1,800 – €9,500 |
  | Sculpture | €240 – €4,800 |
  | Print | €90 – €420 |
  | Photograph | €140 – €480 |
  | Digital download | €25 – €45 |

- **Variants:** prints come "Unframed" or "Framed in oak" (€120 more).
- **Same result every time:** prices, stock and dates are random but seeded from each work's AIC id, so every seed produces the same stores.
- **Images:** copied to the `public` disk under `storage/app/public/artworks/` and served through `/storage` (needs `php artisan storage:link`). The image's alt text is "{title} by {artist}".

Without a `manifest.json`, the seeder falls back to generated artwork (factories with placeholder text and no images).

## Adding or removing works

1. Find candidates:

   ```bash
   php artisan artworks:fetch --discover="harbour at night" --type=Print
   ```

   `--type` is AIC's artwork type: `Painting`, `Print`, `Photograph`, `Sculpture`, `Ceramics`, `Drawing and Watercolor` and others.
2. Add the AIC id to the right group in `FetchArtworks::CATALOGUE`, or remove one. A new group becomes a new collection; add a short description for it in `ArtworkCatalogueSeeder::COLLECTIONS`.
3. Run `php artisan artworks:fetch` to download new images and rewrite the manifest. Images of removed works stay on disk; delete them from `database/seeders/artworks/` by hand.
4. Reseed with `php artisan migrate:fresh --seed`. This wipes your local database.
