<?php

namespace Database\Factories;

use App\Enums\ArtworkStatus;
use App\Enums\ArtworkType;
use App\Models\Artist;
use App\Models\Artwork;
use App\Models\DigitalDetail;
use App\Models\PaintingDetail;
use App\Models\PhotographDetail;
use App\Models\PrintDetail;
use App\Models\SculptureDetail;
use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Defaults to an original painting in draft.
 *
 * @extends Factory<Artwork>
 */
class ArtworkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = Str::title(rtrim(fake()->sentence(3), '.'));

        return [
            'store_id' => Store::factory(),
            'artist_id' => null,
            'artworkable_type' => ArtworkType::Painting->value,
            'artworkable_id' => PaintingDetail::factory(),
            'title' => $title,
            'slug' => Str::slug($title).'-'.Str::lower(Str::random(6)),
            'description' => fake()->paragraph(),
            'year' => fake()->numberBetween(1990, 2026),
            'width_cm' => fake()->numberBetween(20, 150),
            'height_cm' => fake()->numberBetween(20, 150),
            'depth_cm' => null,
            'is_original' => true,
            'price' => fake()->numberBetween(100, 5000) * 100,
            'quantity' => 1,
            'edition_size' => null,
            'status' => ArtworkStatus::Draft,
            'published_at' => null,
        ];
    }

    public function painting(): static
    {
        return $this->for(PaintingDetail::factory(), 'artworkable');
    }

    public function print(): static
    {
        return $this->for(PrintDetail::factory(), 'artworkable');
    }

    public function photograph(): static
    {
        return $this->for(PhotographDetail::factory(), 'artworkable');
    }

    public function sculpture(): static
    {
        return $this->for(SculptureDetail::factory(), 'artworkable')
            ->state(fn (): array => ['depth_cm' => fake()->numberBetween(10, 100)]);
    }

    public function digital(): static
    {
        return $this->for(DigitalDetail::factory(), 'artworkable')
            ->state(['width_cm' => null, 'height_cm' => null, 'depth_cm' => null])
            ->edition(100);
    }

    public function ofType(ArtworkType $type): static
    {
        return match ($type) {
            ArtworkType::Painting => $this->painting(),
            ArtworkType::Print => $this->print(),
            ArtworkType::Photograph => $this->photograph(),
            ArtworkType::Sculpture => $this->sculpture(),
            ArtworkType::Digital => $this->digital(),
        };
    }

    /**
     * A unique original: one in stock, no edition.
     */
    public function original(): static
    {
        return $this->state([
            'is_original' => true,
            'quantity' => 1,
            'edition_size' => null,
        ]);
    }

    /**
     * A limited edition of `$size`, with `$inStock` (default: all) left.
     */
    public function edition(int $size, ?int $inStock = null): static
    {
        return $this->state([
            'is_original' => false,
            'edition_size' => $size,
            'quantity' => $inStock ?? $size,
        ]);
    }

    public function published(): static
    {
        return $this->state([
            'status' => ArtworkStatus::Published,
            'published_at' => now(),
        ]);
    }

    public function sold(): static
    {
        return $this->state([
            'status' => ArtworkStatus::Sold,
            'quantity' => 0,
        ]);
    }

    /**
     * Assign to an artist, keeping the artwork in the artist's store.
     */
    public function byArtist(Artist $artist): static
    {
        return $this->state([
            'artist_id' => $artist->id,
            'store_id' => $artist->store_id,
        ]);
    }
}
