<?php

namespace Tests\Feature\Storefront;

use App\Enums\ArtworkType;
use App\Livewire\Storefront\Artworks\Index;
use App\Models\Artist;
use App\Models\Artwork;
use App\Models\ArtworkVariant;
use App\Support\CurrentStore;
use Livewire\Livewire;

class ArtworksTest extends StorefrontTestCase
{
    public function test_store_home_shows_latest_work(): void
    {
        Artwork::factory()->for($this->store)->published()->create(['title' => 'The Petite Creuse River']);

        $this->get($this->storeUrl())
            ->assertOk()
            ->assertSee('Northlight Gallery')
            ->assertSee('The Petite Creuse River');
    }

    public function test_index_lists_published_and_sold_work_only(): void
    {
        Artwork::factory()->for($this->store)->published()->create(['title' => 'On the wall']);
        Artwork::factory()->for($this->store)->published()->sold()->create(['title' => 'Already sold']);
        Artwork::factory()->for($this->store)->create(['title' => 'Still a draft']);
        Artwork::factory()->for($this->store)->state(['status' => 'archived'])->create(['title' => 'Archived away']);
        Artwork::factory()->for($this->otherStore)->published()->create(['title' => 'Next door']);

        $this->get($this->storeUrl('artworks'))
            ->assertOk()
            ->assertSee('On the wall')
            ->assertSee('Already sold')
            ->assertDontSee('Still a draft')
            ->assertDontSee('Archived away')
            ->assertDontSee('Next door');
    }

    public function test_index_filters_by_type_kind_and_availability(): void
    {
        app(CurrentStore::class)->set($this->store);

        Artwork::factory()->for($this->store)->painting()->published()->create(['title' => 'A painting']);
        Artwork::factory()->for($this->store)->print()->edition(25)->published()->create(['title' => 'A print']);
        Artwork::factory()->for($this->store)->painting()->published()->sold()->create(['title' => 'A sold painting']);

        Livewire::test(Index::class)
            ->set('type', ArtworkType::Print->value)
            ->assertSee('A print')
            ->assertDontSee('A painting')
            ->set('type', '')
            ->set('kind', 'originals')
            ->assertSee('A painting')
            ->assertDontSee('A print')
            ->set('available', true)
            ->assertDontSee('A sold painting')
            ->assertSee('A painting');
    }

    public function test_index_sorts_by_price(): void
    {
        app(CurrentStore::class)->set($this->store);

        Artwork::factory()->for($this->store)->published()->create(['title' => 'Dear', 'price' => 900000]);
        Artwork::factory()->for($this->store)->published()->create(['title' => 'Cheap', 'price' => 10000]);

        Livewire::test(Index::class)
            ->set('sort', 'price-asc')
            ->assertSeeInOrder(['Cheap', 'Dear'])
            ->set('sort', 'price-desc')
            ->assertSeeInOrder(['Dear', 'Cheap']);
    }

    public function test_show_displays_the_wall_label_with_price_in_store_currency(): void
    {
        $artist = Artist::factory()->for($this->store)->create(['name' => 'Claude Monet']);
        $artwork = Artwork::factory()->painting()->byArtist($artist)->published()->create([
            'title' => 'The Beach at Sainte-Adresse',
            'slug' => 'the-beach-at-sainte-adresse',
            'year' => 1867,
            'price' => 450000,
            'width_cm' => 102,
            'height_cm' => 75,
        ]);

        $this->get($this->storeUrl("artworks/{$artwork->slug}"))
            ->assertOk()
            ->assertSee('Claude Monet')
            ->assertSee('The Beach at Sainte-Adresse')
            ->assertSee('1867')
            ->assertSee('€4,500.00')
            ->assertSee('102 × 75 cm')
            ->assertSee('Original, one of one');
    }

    public function test_show_describes_edition_stock_and_variants(): void
    {
        $artwork = Artwork::factory()->for($this->store)->print()->edition(25, 12)->published()->create(['slug' => 'harbour']);
        ArtworkVariant::factory()->create(['artwork_id' => $artwork->id, 'name' => 'Framed in oak', 'price' => 39000, 'quantity' => 2]);

        $this->get($this->storeUrl('artworks/harbour'))
            ->assertOk()
            ->assertSee('12 of 25 left')
            ->assertSee('Framed in oak')
            ->assertSee('€390.00');
    }

    public function test_show_marks_the_last_print_of_an_edition(): void
    {
        Artwork::factory()->for($this->store)->print()->edition(25, 1)->published()->create(['slug' => 'last']);

        $this->get($this->storeUrl('artworks/last'))->assertSee('Last one left of an edition of 25');
    }

    public function test_show_marks_sold_work(): void
    {
        Artwork::factory()->for($this->store)->published()->sold()->create(['slug' => 'gone', 'price' => 450000]);

        $this->get($this->storeUrl('artworks/gone'))
            ->assertOk()
            ->assertSee('Sold')
            ->assertDontSee('€4,500.00');
    }

    public function test_show_hides_drafts_and_other_stores_work(): void
    {
        Artwork::factory()->for($this->store)->create(['slug' => 'draft']);
        Artwork::factory()->for($this->otherStore)->published()->create(['slug' => 'next-door']);

        $this->get($this->storeUrl('artworks/draft'))->assertNotFound();
        $this->get($this->storeUrl('artworks/next-door'))->assertNotFound();
    }

    public function test_an_edition_with_no_copies_left_shows_as_sold_everywhere(): void
    {
        Artwork::factory()->for($this->store)->print()->edition(25, 0)->published()->create([
            'title' => 'Sold out harbour',
            'slug' => 'sold-out-harbour',
            'price' => 27000,
        ]);

        $this->get($this->storeUrl('artworks'))
            ->assertSee('Sold out harbour')
            ->assertDontSee('€270.00');

        $this->get($this->storeUrl('artworks/sold-out-harbour'))
            ->assertSee('Sold')
            ->assertDontSee('€270.00');
    }
}
