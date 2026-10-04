<?php

namespace Tests\Feature;

use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_can_visit_home(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Gallery Row');
        $response->assertSee('Log in');
    }

    public function test_authenticated_users_can_visit_home(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Gallery Row');
        $response->assertSee('Log out');
    }

    public function test_home_invites_visitors_to_open_a_store(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Open your own gallery on the row.')
            ->assertSee(route('stores.create'));
    }

    public function test_home_lists_active_stores_with_links_to_their_addresses(): void
    {
        $store = Store::factory()->create(['name' => 'Northlight Gallery', 'slug' => 'northlight']);
        Store::factory()->pending()->create(['name' => 'Pending Place']);
        Store::factory()->suspended()->create(['name' => 'Suspended Space']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Northlight Gallery')
            ->assertSee($store->url())
            ->assertSee('northlight.gallery-row.test')
            ->assertDontSee('Pending Place')
            ->assertDontSee('Suspended Space');
    }
}
