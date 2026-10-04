<?php

namespace Tests\Feature\Components;

use Tests\TestCase;

class ButtonTest extends TestCase
{
    public function test_default_is_a_medium_ink_button(): void
    {
        $this->blade('<x-button>Save</x-button>')
            ->assertSee('<button', false)
            ->assertSee('type="button"', false)
            ->assertSee('bg-ink-black text-parchment', false)
            ->assertSee('type-body px-5 py-3', false)
            ->assertSee('Save');
    }

    public function test_href_renders_a_link(): void
    {
        $this->blade('<x-button href="/open-a-store">Open your store</x-button>')
            ->assertSee('<a href="/open-a-store"', false)
            ->assertDontSee('<button', false)
            ->assertDontSee('type="button"', false);
    }

    public function test_type_can_be_overridden(): void
    {
        $this->blade('<x-button type="submit">Send</x-button>')
            ->assertSee('type="submit"', false)
            ->assertDontSee('type="button"', false);
    }

    public function test_sizes(): void
    {
        $this->blade('<x-button size="sm">A</x-button>')->assertSee('type-body px-3 py-1.5', false);
        $this->blade('<x-button size="lg">A</x-button>')->assertSee('type-lead px-7 py-4', false);
        $this->blade('<x-button size="huge">A</x-button>')->assertSee('type-body px-5 py-3', false);
    }

    public function test_design_system_colours(): void
    {
        $this->blade('<x-button color="parchment">A</x-button>')->assertSee('bg-parchment text-ink-black', false);
        $this->blade('<x-button color="danger">A</x-button>')->assertSee('bg-error text-parchment', false);
        $this->blade('<x-button color="nonsense">A</x-button>')->assertSee('bg-ink-black', false);
    }

    public function test_solid_accent_uses_large_semibold_text(): void
    {
        $this->blade('<x-button color="accent">Browse</x-button>')
            ->assertSee('type-body-lg px-5 py-3', false)
            ->assertSee('bg-accent text-on-accent font-semibold', false);
    }

    public function test_custom_hex_sets_colours_with_readable_text(): void
    {
        $this->blade('<x-button color="#1f4e6e">A</x-button>')
            ->assertSee('bg-(--button-bg) text-(--button-fg)', false)
            ->assertSee('--button-bg: #1f4e6e; --button-fg: #e2dedb;', false);

        $this->blade('<x-button color="#f2c14e">A</x-button>')
            ->assertSee('--button-fg: #1d1d1b;', false);
    }

    public function test_custom_text_colour_overrides_the_automatic_one(): void
    {
        $this->blade('<x-button color="#f2c14e" text-color="#000000">A</x-button>')
            ->assertSee('--button-bg: #f2c14e; --button-fg: #000000;', false);
    }

    public function test_invalid_hex_falls_back_to_ink_without_a_style(): void
    {
        $this->blade('<x-button color="#fff; background: url(x)">A</x-button>')
            ->assertSee('bg-ink-black', false)
            ->assertDontSee('style=', false)
            ->assertDontSee('url(x)', false);
    }

    public function test_outline_and_ghost_variants_use_ink_text(): void
    {
        $this->blade('<x-button variant="outline" color="accent">A</x-button>')
            ->assertSee('border-2 bg-transparent text-ink-black', false)
            ->assertSee('border-accent', false)
            ->assertDontSee('font-semibold', false);

        $this->blade('<x-button variant="ghost">A</x-button>')
            ->assertSee('bg-transparent text-ink-black hover:bg-parchment', false);
    }

    public function test_extra_attributes_are_passed_through(): void
    {
        $this->blade('<x-button wire:click="save" data-test="save-button" class="w-full">A</x-button>')
            ->assertSee('wire:click="save"', false)
            ->assertSee('data-test="save-button"', false)
            ->assertSee('w-full', false);
    }
}
