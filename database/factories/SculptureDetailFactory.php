<?php

namespace Database\Factories;

use App\Models\SculptureDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SculptureDetail>
 */
class SculptureDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $weight = fake()->randomFloat(2, 0.5, 250);

        return [
            'material' => fake()->randomElement(['bronze', 'marble', 'ceramic', 'wood', 'steel']),
            'weight_kg' => $weight,
            'requires_freight' => $weight > 30,
        ];
    }
}
