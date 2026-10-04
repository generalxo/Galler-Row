<?php

namespace Tests\Feature\Storefront;

use App\Models\Store;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

abstract class StorefrontTestCase extends TestCase
{
    use RefreshDatabase;

    protected Store $store;

    protected Store $otherStore;

    protected function setUp(): void
    {
        parent::setUp();

        $this->store = Store::factory()->create(['name' => 'Northlight Gallery', 'slug' => 'northlight', 'currency' => 'EUR']);
        $this->otherStore = Store::factory()->create(['slug' => 'other']);
    }

    protected function storeUrl(string $path = '/'): string
    {
        return 'http://northlight.gallery-row.test/'.ltrim($path, '/');
    }
}
