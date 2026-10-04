<?php

namespace App\Rules;

use App\Enums\StoreApplicationStatus;
use App\Models\Store;
use App\Models\StoreApplication;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * A subdomain a new store can claim: a valid DNS label, not reserved, and not
 * taken by a store or by an application still waiting for review.
 */
class StoreSubdomain implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! preg_match('/^[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?$/', $value)) {
            $fail('Use lowercase letters, numbers and hyphens only, starting and ending with a letter or number.');

            return;
        }

        if (in_array($value, config('tenancy.reserved_subdomains'), true)) {
            $fail('This address is reserved. Choose another one.');

            return;
        }

        $taken = Store::withTrashed()->where('slug', $value)->exists()
            || StoreApplication::query()
                ->where('subdomain', $value)
                ->where('status', StoreApplicationStatus::Pending)
                ->exists();

        if ($taken) {
            $fail('This address is already taken. Choose another one.');
        }
    }
}
