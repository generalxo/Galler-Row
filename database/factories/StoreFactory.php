<?php

namespace Database\Factories;

use App\Enums\StoreRole;
use App\Enums\StoreStatus;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Store>
 */
class StoreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->company().' Gallery';

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'domain' => null,
            'description' => fake()->paragraph(),
            'status' => StoreStatus::Active,
            'currency' => 'EUR',
            'contact_email' => fake()->companyEmail(),
            'settings' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(['status' => StoreStatus::Pending]);
    }

    public function suspended(): static
    {
        return $this->state(['status' => StoreStatus::Suspended]);
    }

    /**
     * Attach the given user (or a new one) as the store's owner.
     */
    public function ownedBy(?User $user = null): static
    {
        return $this->hasAttached($user ?? User::factory(), ['role' => StoreRole::Owner], 'members');
    }
}
