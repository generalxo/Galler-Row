<?php

namespace Tests\Feature\Storefront;

class AccentTest extends StorefrontTestCase
{
    public function test_valid_accent_is_applied_to_the_storefront(): void
    {
        $this->store->update(['accent_color' => '#1f4e6e', 'on_accent_color' => '#e2dedb']);

        $this->get($this->storeUrl())
            ->assertOk()
            ->assertSee('--color-accent: #1f4e6e; --color-on-accent: #e2dedb;', false);
    }

    public function test_invalid_accent_is_ignored(): void
    {
        $this->store->update(['accent_color' => 'red;', 'on_accent_color' => '#e2dedb']);

        $this->get($this->storeUrl())
            ->assertOk()
            ->assertDontSee('--color-accent', false);
    }

    public function test_no_accent_keeps_the_default(): void
    {
        $this->get($this->storeUrl())
            ->assertOk()
            ->assertDontSee('--color-accent', false);
    }
}
