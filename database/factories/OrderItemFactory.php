<?php

namespace Database\Factories;

use App\Models\Artwork;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'artwork_id' => null,
            'artwork_variant_id' => null,
            'title' => fake()->words(3, true),
            'variant_name' => null,
            'unit_price' => fake()->numberBetween(50, 3000) * 100,
            'quantity' => 1,
            'line_total' => fn (array $attributes): int => $attributes['unit_price'] * $attributes['quantity'],
        ];
    }

    /**
     * Snapshot the given artwork onto the line.
     */
    public function forArtwork(Artwork $artwork, int $quantity = 1): static
    {
        return $this->state([
            'artwork_id' => $artwork->id,
            'title' => $artwork->title,
            'unit_price' => $artwork->price,
            'quantity' => $quantity,
        ]);
    }
}
