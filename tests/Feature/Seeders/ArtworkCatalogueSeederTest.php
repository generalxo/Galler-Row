<?php

namespace Tests\Feature\Seeders;

use App\Enums\ArtworkType;
use App\Models\Artwork;
use App\Models\Store;
use Database\Seeders\ArtworkCatalogueSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ArtworkCatalogueSeederTest extends TestCase
{
    use RefreshDatabase;

    protected string $path;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->path = storage_path('framework/testing/catalogue-'.uniqid());
        File::ensureDirectoryExists("{$this->path}/northlight");
        File::put("{$this->path}/northlight/aic-1.jpg", 'jpeg');
        File::put("{$this->path}/northlight/aic-2.jpg", 'jpeg');
        File::put("{$this->path}/manifest.json", json_encode([
            ['store' => 'northlight', 'collection' => 'Coastlines', 'type' => 'painting', 'source_id' => 1, 'title' => 'Coast of Maine', 'artist' => 'Winslow Homer', 'year' => 1893, 'medium' => 'Oil on canvas', 'width_cm' => 76, 'height_cm' => 61, 'depth_cm' => null, 'image' => 'northlight/aic-1.jpg'],
            ['store' => 'northlight', 'collection' => 'Harbour etchings', 'type' => 'print', 'source_id' => 2, 'title' => 'Gloucester Harbor', 'artist' => 'Winslow Homer', 'year' => 1873, 'medium' => 'Wood engraving', 'width_cm' => 35, 'height_cm' => 23, 'depth_cm' => null, 'image' => 'northlight/aic-2.jpg'],
            ['store' => 'elsewhere', 'collection' => 'Other', 'type' => 'painting', 'source_id' => 3, 'title' => 'Not ours', 'artist' => null, 'year' => null, 'medium' => null, 'width_cm' => null, 'height_cm' => null, 'depth_cm' => null, 'image' => 'elsewhere/aic-3.jpg'],
        ]));
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->path);

        parent::tearDown();
    }

    public function test_it_seeds_a_store_catalogue_from_the_manifest(): void
    {
        $store = Store::factory()->create(['slug' => 'northlight']);
        $seeder = new ArtworkCatalogueSeeder($this->path);

        $this->assertTrue($seeder->hasCatalogue($store));
        $artworks = $seeder->seed($store);

        $this->assertCount(2, $artworks);
        $this->assertSame(1, $store->artists()->count());
        $this->assertSame(['Coastlines', 'Harbour etchings'], $store->collections()->orderBy('position')->pluck('name')->all());
        $this->assertTrue($store->collections()->where('name', 'Coastlines')->value('is_featured'));

        $painting = Artwork::query()->where('title', 'Coast of Maine')->sole();
        $this->assertSame(ArtworkType::Painting, $painting->type);
        $this->assertTrue($painting->is_original);
        $this->assertSame('Oil', $painting->artworkable->medium);
        $this->assertSame('canvas', $painting->artworkable->surface);
        Storage::disk('public')->assertExists('artworks/northlight/aic-1.jpg');
        $this->assertSame('Coast of Maine by Winslow Homer', $painting->images->first()->alt);

        $print = Artwork::query()->where('title', 'Gloucester Harbor')->sole();
        $this->assertFalse($print->is_original);
        $this->assertNotNull($print->edition_size);
        $this->assertLessThanOrEqual($print->edition_size, $print->quantity);
        $this->assertSame(['Unframed', 'Framed in oak'], $print->variants()->orderBy('position')->pluck('name')->all());
    }

    public function test_prices_and_stock_are_the_same_on_every_seed(): void
    {
        $first = (new ArtworkCatalogueSeeder($this->path))->seed(Store::factory()->create(['slug' => 'northlight']));
        $prices = array_map(fn (Artwork $artwork): array => [$artwork->price, $artwork->quantity], $first);

        Store::query()->forceDelete();
        $second = (new ArtworkCatalogueSeeder($this->path))->seed(Store::factory()->create(['slug' => 'northlight']));

        $this->assertSame($prices, array_map(fn (Artwork $artwork): array => [$artwork->price, $artwork->quantity], $second));
    }

    public function test_stores_without_entries_have_no_catalogue(): void
    {
        $this->assertFalse((new ArtworkCatalogueSeeder($this->path))->hasCatalogue(Store::factory()->create(['slug' => 'empty'])));
    }
}
