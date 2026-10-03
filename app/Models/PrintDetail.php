<?php

namespace App\Models;

use App\Models\Concerns\IsArtworkDetail;
use Database\Factories\PrintDetailFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Print-specific details of an artwork.
 *
 * @property int $id
 * @property string $print_method
 * @property string|null $paper
 * @property bool $is_signed
 * @property bool $is_numbered
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Artwork|null $artwork
 */
#[Fillable(['print_method', 'paper', 'is_signed', 'is_numbered'])]
class PrintDetail extends Model
{
    /** @use HasFactory<PrintDetailFactory> */
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
            'is_numbered' => 'boolean',
        ];
    }
}
