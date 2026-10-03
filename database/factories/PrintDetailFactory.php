<?php

namespace Database\Factories;

use App\Models\PrintDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PrintDetail>
 */
class PrintDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'print_method' => fake()->randomElement(['giclée', 'screen print', 'etching', 'lithograph', 'linocut']),
            'paper' => fake()->randomElement(['Hahnemühle Photo Rag', 'Somerset Velvet', 'Arches 88', null]),
            'is_signed' => true,
            'is_numbered' => true,
        ];
    }
}
