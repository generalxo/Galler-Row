<?php

namespace Database\Factories;

use App\Models\Artwork;
use App\Models\ArtworkVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ArtworkVariant>
 */
class ArtworkVariantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'artwork_id' => Artwork::factory()->print()->edition(50),
            'name' => fake()->randomElement(['A4', 'A3', 'A2']).', '.fake()->randomElement(['unframed', 'framed']),
            'sku' => fake()->unique()->bothify('GR-####-??'),
            'price' => fake()->numberBetween(50, 600) * 100,
            'quantity' => fake()->numberBetween(0, 20),
            'position' => 0,
        ];
    }
}
