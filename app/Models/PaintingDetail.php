<?php

namespace App\Models;

use App\Models\Concerns\IsArtworkDetail;
use Database\Factories\PaintingDetailFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Painting-specific details of an artwork.
 *
 * @property int $id
 * @property string $medium
 * @property string $surface
 * @property bool $is_framed
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Artwork|null $artwork
 */
#[Fillable(['medium', 'surface', 'is_framed'])]
class PaintingDetail extends Model
{
    /** @use HasFactory<PaintingDetailFactory> */
    use HasFactory, IsArtworkDetail;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_framed' => 'boolean',
        ];
    }
}
