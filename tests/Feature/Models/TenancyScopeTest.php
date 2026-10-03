<?php

namespace Tests\Feature\Models;

use App\Models\Artwork;
use App\Models\Store;
use App\Support\CurrentStore;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenancyScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_queries_only_see_the_current_stores_rows(): void
    {
        [$storeA, $storeB] = Store::factory()->count(2)->create();
        $ownArtwork = Artwork::factory()->for($storeA)->create();
        Artwork::factory()->for($storeB)->create();

        app(CurrentStore::class)->set($storeA);

        $this->assertSame([$ownArtwork->id], Artwork::pluck('id')->all());
    }

    public function test_all_rows_are_visible_without_a_current_store(): void
    {
        Artwork::factory()->count(2)->create();

        $this->assertSame(2, Artwork::count());
    }

    public function test_new_rows_are_assigned_to_the_current_store(): void
    {
        $store = Store::factory()->create();
        app(CurrentStore::class)->set($store);

        $artwork = Artwork::factory()->create(['store_id' => null]);

        $this->assertSame($store->id, $artwork->store_id);
    }
}
