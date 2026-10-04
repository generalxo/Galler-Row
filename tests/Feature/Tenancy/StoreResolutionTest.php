<?php

namespace Tests\Feature\Tenancy;

use App\Models\Artwork;
use App\Models\Store;
use App\Support\CurrentStore;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class StoreResolutionTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_domain_serves_the_platform_without_a_store(): void
    {
        $this->get('http://gallery-row.test/')
            ->assertOk()
            ->assertSee('Gallery Row');

        $this->assertFalse(app(CurrentStore::class)->has());
    }

    public function test_www_redirects_to_the_root_domain(): void
    {
        $this->get('http://www.gallery-row.test/?x=1')
            ->assertRedirect('http://gallery-row.test/?x=1')
            ->assertStatus(301);
    }

    public function test_store_subdomain_serves_the_storefront(): void
    {
        $store = Store::factory()->create(['name' => 'Northlight Gallery', 'slug' => 'northlight']);

        $this->get('http://northlight.gallery-row.test/')
            ->assertOk()
            ->assertSee('Northlight Gallery');

        $this->assertTrue(app(CurrentStore::class)->get()?->is($store));
    }

    public function test_custom_domain_serves_the_storefront(): void
    {
        Store::factory()->create(['name' => 'Clay & Kiln', 'slug' => 'clay-and-kiln', 'domain' => 'clayandkiln.test']);

        $this->get('http://clayandkiln.test/')
            ->assertOk()
            ->assertSee('Clay &amp; Kiln', false);
    }

    public function test_unknown_hosts_are_not_found(): void
    {
        $this->get('http://nope.gallery-row.test/')->assertNotFound();
        $this->get('http://example.test/')->assertNotFound();
        $this->get('http://a.b.gallery-row.test/')->assertNotFound();
    }

    public function test_reserved_subdomains_never_resolve(): void
    {
        Store::factory()->create(['slug' => 'admin']);

        $this->get('http://admin.gallery-row.test/')->assertNotFound();
    }

    public function test_inactive_stores_are_not_found(): void
    {
        Store::factory()->pending()->create(['slug' => 'pending-store']);
        Store::factory()->suspended()->create(['slug' => 'suspended-store']);

        $this->get('http://pending-store.gallery-row.test/')->assertNotFound();
        $this->get('http://suspended-store.gallery-row.test/')->assertNotFound();
    }

    public function test_store_hosts_only_see_their_own_artwork(): void
    {
        Route::middleware('web')->get('/_test/artworks', fn () => Artwork::query()->pluck('title'));

        $mine = Store::factory()->create(['slug' => 'mine']);
        $theirs = Store::factory()->create(['slug' => 'theirs']);
        Artwork::factory()->for($mine)->create(['title' => 'Mine']);
        Artwork::factory()->for($theirs)->create(['title' => 'Theirs']);

        $this->get('http://mine.gallery-row.test/_test/artworks')
            ->assertOk()
            ->assertExactJson(['Mine']);
    }
}
