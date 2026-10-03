<?php

namespace Database\Factories;

use App\Models\Artwork;
use App\Models\Image;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Image>
 */
class ImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'imageable_type' => 'artwork',
            'imageable_id' => Artwork::factory(),
            'collection' => 'gallery',
            'path' => 'images/'.Str::uuid().'.jpg',
            'alt' => fake()->sentence(4),
            'position' => 0,
        ];
    }
}
