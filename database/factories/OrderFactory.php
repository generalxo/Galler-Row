<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->numberBetween(50, 3000) * 100;
        $shipping = 1500;

        $address = [
            'name' => fake()->name(),
            'line1' => fake()->streetAddress(),
            'line2' => null,
            'city' => fake()->city(),
            'postal_code' => fake()->postcode(),
            'country' => fake()->countryCode(),
        ];

        return [
            'store_id' => Store::factory(),
            'user_id' => null,
            'number' => 'GR-'.Str::upper(Str::random(8)),
            'email' => fake()->safeEmail(),
            'status' => OrderStatus::Paid,
            'currency' => 'EUR',
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'tax' => 0,
            'total' => $subtotal + $shipping,
            'shipping_address' => $address,
            'billing_address' => $address,
            'placed_at' => now(),
            'paid_at' => now(),
            'shipped_at' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state([
            'status' => OrderStatus::Pending,
            'paid_at' => null,
        ]);
    }
}
