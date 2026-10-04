<?php

namespace Tests\Unit;

use App\Models\Store;
use Tests\TestCase;

class StoreUrlTest extends TestCase
{
    public function test_subdomain_url(): void
    {
        $store = new Store(['slug' => 'northlight']);

        $this->assertSame('http://northlight.gallery-row.test/artworks', $store->url('artworks'));
    }

    public function test_custom_domain_url(): void
    {
        $store = new Store(['slug' => 'clay-and-kiln', 'domain' => 'clayandkiln.test']);

        $this->assertSame('http://clayandkiln.test/', $store->url());
    }

    public function test_url_keeps_the_app_url_scheme_and_port(): void
    {
        config(['app.url' => 'https://gallery-row.test:8000']);

        $store = new Store(['slug' => 'northlight']);

        $this->assertSame('https://northlight.gallery-row.test:8000/', $store->url('/'));
    }
}
