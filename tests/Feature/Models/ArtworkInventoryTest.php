<?php

namespace Tests\Feature\Models;

use App\Exceptions\InvalidArtworkInventory;
use App\Models\Artwork;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArtworkInventoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_edition_with_one_left_is_not_an_original(): void
    {
        $lastPrint = Artwork::factory()->print()->edition(50, inStock: 1)->create();

        $this->assertFalse($lastPrint->fresh()?->is_original);
        $this->assertFalse(Artwork::originals()->whereKey($lastPrint->id)->exists());
        $this->assertTrue(Artwork::editions()->whereKey($lastPrint->id)->exists());
    }

    public function test_original_cannot_have_more_than_one_in_stock(): void
    {
        $this->expectException(InvalidArtworkInventory::class);

        Artwork::factory()->original()->create(['quantity' => 2]);
    }

    public function test_original_cannot_have_an_edition_size(): void
    {
        $this->expectException(InvalidArtworkInventory::class);

        Artwork::factory()->original()->create(['edition_size' => 10]);
    }

    public function test_edition_requires_an_edition_size(): void
    {
        $this->expectException(InvalidArtworkInventory::class);

        Artwork::factory()->create(['is_original' => false, 'edition_size' => null]);
    }

    public function test_stock_cannot_exceed_edition_size(): void
    {
        $this->expectException(InvalidArtworkInventory::class);

        Artwork::factory()->edition(10, inStock: 11)->create();
    }

    public function test_sold_out_original_is_still_an_original(): void
    {
        $artwork = Artwork::factory()->original()->sold()->create();

        $this->assertTrue($artwork->is_original);
        $this->assertFalse(Artwork::available()->whereKey($artwork->id)->exists());
    }
}
