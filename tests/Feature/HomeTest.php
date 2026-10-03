<?php

namespace Tests\Feature;

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
}
