<?php

namespace App\Models;

use App\Models\Concerns\IsArtworkDetail;
use Database\Factories\PhotographDetailFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Photograph-specific details of an artwork.
 *
 * @property int $id
 * @property string|null $print_process
 * @property string|null $paper
 * @property bool $is_signed
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Artwork|null $artwork
 */
#[Fillable(['print_process', 'paper', 'is_signed'])]
class PhotographDetail extends Model
{
    /** @use HasFactory<PhotographDetailFactory> */
    use HasFactory, IsArtworkDetail;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_signed' => 'boolean',
        ];
    }
}
