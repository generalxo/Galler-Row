<?php

namespace Tests\Feature\Models;

use App\Enums\ArtworkType;
use App\Models\Artwork;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ArtworkPolymorphismTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{ArtworkType}>
     */
    public static function types(): array
    {
        $cases = [];

        foreach (ArtworkType::cases() as $type) {
            $cases[$type->value] = [$type];
        }

        return $cases;
    }

    #[DataProvider('types')]
    public function test_each_type_resolves_its_detail_model(ArtworkType $type): void
    {
        $artwork = Artwork::factory()->ofType($type)->create()->fresh();

        $this->assertNotNull($artwork);
        $this->assertSame($type, $artwork->type);
        $this->assertInstanceOf($type->detailModel(), $artwork->artworkable);
        $this->assertTrue($artwork->artworkable->artwork?->is($artwork));
    }

    public function test_morph_type_is_stored_as_short_key(): void
    {
        Artwork::factory()->sculpture()->create();

        $this->assertDatabaseHas('artworks', ['artworkable_type' => 'sculpture']);
    }

    public function test_of_type_scope_filters_by_type(): void
    {
        Artwork::factory()->painting()->create();
        $print = Artwork::factory()->print()->edition(10)->create();

        $this->assertSame([$print->id], Artwork::ofType(ArtworkType::Print)->pluck('id')->all());
    }
}
