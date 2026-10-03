<?php

namespace App\Models;

use App\Enums\DigitalLicense;
use App\Models\Concerns\IsArtworkDetail;
use Database\Factories\DigitalDetailFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Digital-artwork details: the downloadable file and its license.
 *
 * @property int $id
 * @property string $file_format
 * @property string|null $resolution
 * @property DigitalLicense $license
 * @property string $file_path Path on the private disk.
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Artwork|null $artwork
 */
#[Fillable(['file_format', 'resolution', 'license', 'file_path'])]
#[Hidden(['file_path'])]
class DigitalDetail extends Model
{
    /** @use HasFactory<DigitalDetailFactory> */
    use HasFactory, IsArtworkDetail;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'license' => DigitalLicense::class,
        ];
    }
}
