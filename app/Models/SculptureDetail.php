<?php

namespace App\Models;

use App\Models\Concerns\IsArtworkDetail;
use Database\Factories\SculptureDetailFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Sculpture-specific details of an artwork.
 *
 * @property int $id
 * @property string $material
 * @property string|null $weight_kg
 * @property bool $requires_freight
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Artwork|null $artwork
 */
#[Fillable(['material', 'weight_kg', 'requires_freight'])]
class SculptureDetail extends Model
{
    /** @use HasFactory<SculptureDetailFactory> */
    use HasFactory, IsArtworkDetail;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'weight_kg' => 'decimal:2',
            'requires_freight' => 'boolean',
        ];
    }
}
