<?php

namespace Database\Factories;

use App\Enums\StoreApplicationStatus;
use App\Models\StoreApplication;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<StoreApplication>
 */
class StoreApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $storeName = fake()->unique()->lastName().' Studio';

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'store_name' => $storeName,
            'subdomain' => Str::slug($storeName),
            'website' => null,
            'message' => fake()->paragraph(),
            'status' => StoreApplicationStatus::Pending,
        ];
    }

    public function approved(): static
    {
        return $this->state(['status' => StoreApplicationStatus::Approved]);
    }

    public function rejected(): static
    {
        return $this->state(['status' => StoreApplicationStatus::Rejected]);
    }
}
