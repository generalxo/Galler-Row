<?php

namespace Tests\Feature\Storefront;

use App\Models\Artist;
use App\Models\Artwork;
use App\Models\Collection;

class CollectionsAndArtistsTest extends StorefrontTestCase
{
    public function test_collections_index_and_show(): void
    {
        $collection = Collection::factory()->for($this->store)->create(['name' => 'Coastlines', 'slug' => 'coastlines']);
        $shown = Artwork::factory()->for($this->store)->published()->create(['title' => 'Coast of Maine']);
        $draft = Artwork::factory()->for($this->store)->create(['title' => 'Unfinished coast']);
        $collection->artworks()->attach([$shown->id => ['position' => 0], $draft->id => ['position' => 1]]);

        $this->get($this->storeUrl('collections'))
            ->assertOk()
            ->assertSee('Coastlines')
            ->assertSee('1 work');

        $this->get($this->storeUrl('collections/coastlines'))
            ->assertOk()
            ->assertSee('Coast of Maine')
            ->assertDontSee('Unfinished coast');
    }

    public function test_artists_index_and_show(): void
    {
        $artist = Artist::factory()->for($this->store)->create(['name' => 'Winslow Homer', 'slug' => 'winslow-homer']);
        Artwork::factory()->byArtist($artist)->published()->create(['title' => 'Coast of Maine']);
        Artist::factory()->for($this->store)->create(['name' => 'Nobody On Show']);

        $this->get($this->storeUrl('artists'))
            ->assertOk()
            ->assertSee('Winslow Homer')
            ->assertDontSee('Nobody On Show');

        $this->get($this->storeUrl('artists/winslow-homer'))
            ->assertOk()
            ->assertSee('Coast of Maine');
    }

    public function test_other_stores_collections_and_artists_are_not_found(): void
    {
        Collection::factory()->for($this->otherStore)->create(['slug' => 'theirs']);
        Artist::factory()->for($this->otherStore)->create(['slug' => 'their-artist']);

        $this->get($this->storeUrl('collections/theirs'))->assertNotFound();
        $this->get($this->storeUrl('artists/their-artist'))->assertNotFound();
    }

    public function test_storefront_pages_are_not_on_the_platform_domain(): void
    {
        $this->get('http://gallery-row.test/artworks')->assertNotFound();
    }
}
