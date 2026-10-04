<?php

namespace App\Livewire;

use App\Models\StoreApplication;
use App\Rules\StoreSubdomain;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * The "Open your store" form: saves a store application for a platform
 * admin to review. Nothing is emailed; this is a demo.
 */
#[Title('Open your store')]
class OpenStore extends Component
{
    public string $name = '';

    public string $email = '';

    public string $storeName = '';

    public string $subdomain = '';

    public string $website = '';

    public string $message = '';

    public bool $subdomainEdited = false;

    public bool $submitted = false;

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'storeName' => ['required', 'string', 'max:255'],
            'subdomain' => ['required', new StoreSubdomain],
            'website' => ['nullable', 'url', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return [
            'storeName' => 'store name',
            'subdomain' => 'address',
        ];
    }

    /**
     * Suggest an address from the store name until the applicant types their own.
     */
    public function updatedStoreName(string $value): void
    {
        if (! $this->subdomainEdited) {
            $this->subdomain = Str::limit(Str::slug($value), 63, '');
        }
    }

    public function updatedSubdomain(): void
    {
        $this->subdomainEdited = true;
    }

    public function submit(): void
    {
        $key = 'open-store:'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->addError('message', 'Too many applications from this connection. Try again in an hour.');

            return;
        }

        $this->subdomain = strtolower(trim($this->subdomain));
        $validated = $this->validate();

        RateLimiter::hit($key, 3600);

        StoreApplication::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'store_name' => $validated['storeName'],
            'subdomain' => $validated['subdomain'],
            'website' => $validated['website'] ?: null,
            'message' => $validated['message'],
        ]);

        $this->submitted = true;
    }

    public function render(): View
    {
        return view('livewire.open-store', [
            'rootDomain' => config('tenancy.root_domain'),
        ]);
    }
}
