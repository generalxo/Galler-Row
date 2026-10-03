<?php

namespace Database\Factories;

use App\Enums\DigitalLicense;
use App\Models\DigitalDetail;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<DigitalDetail>
 */
class DigitalDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $format = fake()->randomElement(['png', 'tiff', 'jpg']);

        return [
            'file_format' => $format,
            'resolution' => fake()->randomElement(['4000x3000', '6000x4000', '8000x6000']),
            'license' => fake()->randomElement(DigitalLicense::cases()),
            'file_path' => 'artworks/'.Str::uuid().'.'.$format,
        ];
    }
}
