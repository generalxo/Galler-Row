<?php

namespace Tests\Feature;

use App\Enums\StoreApplicationStatus;
use App\Livewire\OpenStore;
use App\Models\Store;
use App\Models\StoreApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Tests\TestCase;

class OpenStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_open_store_page_is_on_the_platform_domain(): void
    {
        $this->get(route('stores.create'))
            ->assertOk()
            ->assertSee('Send application');
    }

    public function test_open_store_page_is_not_on_store_hosts(): void
    {
        Store::factory()->create(['slug' => 'northlight']);

        $this->get('http://northlight.gallery-row.test/open-a-store')->assertNotFound();
    }

    public function test_valid_application_is_saved_as_pending(): void
    {
        $this->form()
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('submitted', true)
            ->assertSee('Application sent')
            ->assertSee('paper-moon-press.gallery-row.test');

        $application = StoreApplication::sole();
        $this->assertSame('Paper Moon Press', $application->store_name);
        $this->assertSame('paper-moon-press', $application->subdomain);
        $this->assertSame(StoreApplicationStatus::Pending, $application->status);
        $this->assertNull($application->website);
    }

    public function test_fields_are_required(): void
    {
        Livewire::test(OpenStore::class)
            ->call('submit')
            ->assertHasErrors(['name' => 'required', 'email' => 'required', 'storeName' => 'required', 'subdomain' => 'required', 'message' => 'required']);

        $this->assertDatabaseCount('store_applications', 0);
    }

    public function test_email_and_website_must_be_valid(): void
    {
        $this->form()
            ->set('email', 'not-an-email')
            ->set('website', 'not a url')
            ->call('submit')
            ->assertHasErrors(['email' => 'email', 'website' => 'url']);
    }

    public function test_address_is_suggested_from_the_store_name_until_edited(): void
    {
        Livewire::test(OpenStore::class)
            ->set('storeName', 'Clay & Kiln Two')
            ->assertSet('subdomain', 'clay-kiln-two')
            ->set('subdomain', 'my-kiln')
            ->set('storeName', 'Something Else')
            ->assertSet('subdomain', 'my-kiln');
    }

    public function test_address_must_be_a_valid_label(): void
    {
        foreach (['Has Spaces', '-leading', 'trailing-', 'under_score', str_repeat('a', 64)] as $subdomain) {
            $this->form()->set('subdomain', $subdomain)->call('submit')->assertHasErrors('subdomain');
        }
    }

    public function test_address_cannot_be_reserved(): void
    {
        $this->form()->set('subdomain', 'admin')->call('submit')->assertHasErrors('subdomain');
    }

    public function test_address_cannot_be_taken_by_a_store(): void
    {
        Store::factory()->create(['slug' => 'northlight']);

        $this->form()->set('subdomain', 'northlight')->call('submit')->assertHasErrors('subdomain');
    }

    public function test_address_cannot_be_taken_by_a_pending_application(): void
    {
        StoreApplication::factory()->create(['subdomain' => 'paper-moon-press']);

        $this->form()->call('submit')->assertHasErrors('subdomain');
    }

    public function test_address_of_a_rejected_application_is_free_again(): void
    {
        StoreApplication::factory()->rejected()->create(['subdomain' => 'paper-moon-press']);

        $this->form()->call('submit')->assertHasNoErrors();
    }

    public function test_applications_are_rate_limited(): void
    {
        foreach (range(1, 5) as $i) {
            $this->form()->set('subdomain', "store-{$i}")->call('submit')->assertHasNoErrors();
        }

        $this->form()->set('subdomain', 'store-6')->call('submit')->assertHasErrors('message');

        $this->assertDatabaseCount('store_applications', 5);
    }

    /**
     * @return Testable
     */
    protected function form()
    {
        return Livewire::test(OpenStore::class)
            ->set('name', 'Ines Albrecht')
            ->set('email', 'ines@example.com')
            ->set('storeName', 'Paper Moon Press')
            ->set('message', 'Risograph prints in small editions.');
    }
}
