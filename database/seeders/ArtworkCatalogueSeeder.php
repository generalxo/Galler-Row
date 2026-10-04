<?php

namespace Database\Seeders;

use App\Enums\ArtworkStatus;
use App\Enums\ArtworkType;
use App\Enums\DigitalLicense;
use App\Models\Artist;
use App\Models\Artwork;
use App\Models\ArtworkVariant;
use App\Models\Collection;
use App\Models\DigitalDetail;
use App\Models\Image;
use App\Models\PaintingDetail;
use App\Models\PhotographDetail;
use App\Models\PrintDetail;
use App\Models\SculptureDetail;
use App\Models\Store;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Seeds a store's catalogue from the public-domain artworks downloaded by
 * `php artisan artworks:fetch` (database/seeders/artworks/manifest.json).
 *
 * Prices, stock and dates are made up, but deterministic per artwork, so
 * every seed produces the same store.
 */
class ArtworkCatalogueSeeder
{
    /**
     * Short descriptions for the collections the fetch command creates.
     *
     * @var array<string, string>
     */
    protected const array COLLECTIONS = [
        'Mountains and rivers' => 'Valleys, peaks and slow water, painted outdoors and finished in the studio.',
        'Coastlines' => 'Cliffs, beaches and weather coming in off the sea.',
        'Harbour etchings' => 'Boats, quays and harbour light, printed in small editions.',
        'Sea photographs' => 'Early photographs of open water and shorelines, printed in limited editions.',
        'Still lifes' => 'Fruit, flowers and the objects on the table, looked at closely.',
        'Quiet rooms' => 'Interiors, readers and the light through a window.',
        'Prints' => 'Etchings, aquatints and lithographs of rooms and still lifes.',
        'Digital downloads' => 'High-resolution files of selected paintings for printing at home.',
        'Vessels' => 'Teapots, pitchers and vases in stoneware, earthenware and porcelain.',
        'Small sculpture' => 'Bronze, plaster and terracotta pieces sized for a shelf or a table.',
    ];

    public function __construct(
        protected string $directory = '',
    ) {
        $this->directory = $directory ?: database_path('seeders/artworks');
    }

    /**
     * Whether the manifest has artworks for this store.
     */
    public function hasCatalogue(Store $store): bool
    {
        return $this->entries($store) !== [];
    }

    /**
     * Create the store's artists, collections, artworks and images.
     *
     * @return list<Artwork>
     */
    public function seed(Store $store): array
    {
        $artworks = [];
        $artists = [];
        $collections = [];
        $slugs = [];

        foreach ($this->entries($store) as $entry) {
            mt_srand($entry['source_id'] + crc32($entry['type']));

            $collection = $collections[$entry['collection']] ??= Collection::query()->create([
                'store_id' => $store->id,
                'name' => $entry['collection'],
                'slug' => Str::slug($entry['collection']),
                'description' => self::COLLECTIONS[$entry['collection']] ?? null,
                'position' => count($collections),
                'is_featured' => $collections === [],
            ]);

            $artist = null;

            if ($entry['artist'] !== null) {
                $artist = $artists[$entry['artist']] ??= Artist::query()->create([
                    'store_id' => $store->id,
                    'name' => $entry['artist'],
                    'slug' => Str::slug($entry['artist']),
                ]);
            }

            $artwork = $this->artwork($store, $entry, $artist, $slugs);
            $artwork->collections()->attach($collection, ['position' => $collection->artworks()->count()]);

            $this->image($artwork, $entry);

            if ($entry['type'] === 'print') {
                $this->variants($artwork);
            }

            $artworks[] = $artwork;
        }

        mt_srand();

        return $artworks;
    }

    /**
     * @param  array<string, mixed>  $entry
     * @param  array<string, true>  $slugs
     */
    protected function artwork(Store $store, array $entry, ?Artist $artist, array &$slugs): Artwork
    {
        $type = ArtworkType::from($entry['type']);
        $isDigital = $type === ArtworkType::Digital;
        $title = $isDigital ? "{$entry['title']} (digital download)" : $entry['title'];

        $slug = Str::slug($title);
        $slug = isset($slugs[$slug]) ? "{$slug}-{$entry['source_id']}" : $slug;
        $slugs[$slug] = true;

        $artwork = new Artwork([
            'store_id' => $store->id,
            'artist_id' => $artist?->id,
            'title' => $title,
            'slug' => $slug,
            'description' => $this->description($entry),
            'year' => $entry['year'],
            'width_cm' => $isDigital ? null : $entry['width_cm'],
            'height_cm' => $isDigital ? null : $entry['height_cm'],
            'depth_cm' => $isDigital ? null : $entry['depth_cm'],
            'published_at' => now()->subDays(mt_rand(0, 90))->subHours(mt_rand(0, 23)),
            ...$this->inventory($type),
        ]);

        $artwork->artworkable()->associate($this->detail($type, $entry));
        $artwork->ensureValidInventory();
        $artwork->save();

        return $artwork;
    }

    /**
     * Price, stock and status by type. Originals: paintings and sculpture.
     *
     * @return array<string, mixed>
     */
    protected function inventory(ArtworkType $type): array
    {
        [$min, $max, $edition] = match ($type) {
            ArtworkType::Painting => [1800, 9500, null],
            ArtworkType::Sculpture => [240, 4800, null],
            ArtworkType::Print => [90, 420, mt_rand(0, 1) ? 25 : 50],
            ArtworkType::Photograph => [140, 480, 30],
            ArtworkType::Digital => [25, 45, 100],
        };

        $price = (int) round(mt_rand($min, $max) / ($max > 1000 ? 50 : 5)) * ($max > 1000 ? 50 : 5) * 100;

        if ($edition === null) {
            $sold = mt_rand(1, 8) === 1;

            return [
                'is_original' => true,
                'edition_size' => null,
                'quantity' => $sold ? 0 : 1,
                'price' => $price,
                'status' => $sold ? ArtworkStatus::Sold : ArtworkStatus::Published,
            ];
        }

        $roll = mt_rand(1, 10);

        return [
            'is_original' => false,
            'edition_size' => $edition,
            // Mostly well stocked, sometimes down to the last one.
            'quantity' => $roll === 1 ? 1 : mt_rand((int) ($edition / 3), $edition),
            'price' => $price,
            'status' => ArtworkStatus::Published,
        ];
    }

    /**
     * @param  array<string, mixed>  $entry
     */
    protected function detail(ArtworkType $type, array $entry): Model
    {
        $medium = Str::limit((string) ($entry['medium'] ?? 'Mixed media'), 250, '');

        return match ($type) {
            ArtworkType::Painting => PaintingDetail::query()->create([
                'medium' => Str::before($medium, ' on ') ?: $medium,
                'surface' => str_contains($medium, ' on ') ? Str::after($medium, ' on ') : 'unrecorded support',
                'is_framed' => mt_rand(0, 1) === 1,
            ]),
            ArtworkType::Print => PrintDetail::query()->create([
                'print_method' => $medium,
                'is_signed' => false,
                'is_numbered' => true,
            ]),
            ArtworkType::Photograph => PhotographDetail::query()->create([
                'print_process' => $medium,
                'is_signed' => false,
            ]),
            ArtworkType::Sculpture => SculptureDetail::query()->create([
                'material' => $medium,
                'requires_freight' => ($entry['height_cm'] ?? 0) > 60,
            ]),
            ArtworkType::Digital => DigitalDetail::query()->create([
                'file_format' => 'jpg',
                'resolution' => '6000x4500',
                'license' => DigitalLicense::Personal,
                'file_path' => "downloads/aic-{$entry['source_id']}.jpg",
            ]),
        };
    }

    /**
     * @param  array<string, mixed>  $entry
     */
    protected function description(array $entry): string
    {
        $by = $entry['artist'] ?? 'an unknown maker';
        $year = $entry['year'] ? " ({$entry['year']})" : '';
        $lead = $entry['type'] === 'digital'
            ? 'A high-resolution file for printing at home, for personal use.'
            : rtrim((string) ($entry['medium'] ?? ''), '.').'.';

        return "{$lead} Demo listing: the original by {$by}{$year} is in the collection of the Art Institute of Chicago, which released this image under CC0.";
    }

    /**
     * @param  array<string, mixed>  $entry
     */
    protected function image(Artwork $artwork, array $entry): void
    {
        $path = "artworks/{$entry['image']}";

        if (! Storage::disk('public')->exists($path)) {
            Storage::disk('public')->put($path, File::get("{$this->directory}/{$entry['image']}"));
        }

        $alt = $entry['artist'] ? "{$entry['title']} by {$entry['artist']}" : $entry['title'];

        $image = new Image(['path' => $path, 'alt' => $alt, 'position' => 0]);
        $image->imageable()->associate($artwork);
        $image->save();
    }

    protected function variants(Artwork $artwork): void
    {
        $framed = min(3, $artwork->quantity);

        ArtworkVariant::query()->create([
            'artwork_id' => $artwork->id,
            'name' => 'Unframed',
            'price' => $artwork->price,
            'quantity' => $artwork->quantity - $framed,
            'position' => 0,
        ]);
        ArtworkVariant::query()->create([
            'artwork_id' => $artwork->id,
            'name' => 'Framed in oak',
            'price' => $artwork->price + 12000,
            'quantity' => $framed,
            'position' => 1,
        ]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function entries(Store $store): array
    {
        $manifest = "{$this->directory}/manifest.json";

        if (! File::exists($manifest)) {
            return [];
        }

        /** @var list<array<string, mixed>> $entries */
        $entries = json_decode(File::get($manifest), true, flags: JSON_THROW_ON_ERROR);

        return array_values(array_filter($entries, fn (array $entry): bool => $entry['store'] === $store->slug));
    }
}
