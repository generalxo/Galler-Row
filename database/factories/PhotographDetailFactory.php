<?php

namespace Database\Factories;

use App\Models\PhotographDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PhotographDetail>
 */
class PhotographDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'print_process' => fake()->randomElement(['archival pigment', 'silver gelatin', 'C-type', null]),
            'paper' => fake()->randomElement(['baryta', 'matte fibre', null]),
            'is_signed' => fake()->boolean(),
        ];
    }
}
