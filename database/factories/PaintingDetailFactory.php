<?php

namespace Database\Factories;

use App\Models\PaintingDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PaintingDetail>
 */
class PaintingDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'medium' => fake()->randomElement(['oil', 'acrylic', 'watercolour', 'gouache', 'mixed media']),
            'surface' => fake()->randomElement(['canvas', 'linen', 'panel', 'paper']),
            'is_framed' => fake()->boolean(),
        ];
    }
}
