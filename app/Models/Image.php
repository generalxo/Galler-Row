<?php

namespace App\Models;

use Database\Factories\ImageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * An image attached to an artwork, store or artist.
 *
 * @property int $id
 * @property string $imageable_type
 * @property int $imageable_id
 * @property string $collection gallery, logo, banner or portrait
 * @property string $path
 * @property string|null $alt
 * @property int $position
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['collection', 'path', 'alt', 'position'])]
class Image extends Model
{
    /** @use HasFactory<ImageFactory> */
    use HasFactory;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'collection' => 'gallery',
        'position' => 0,
    ];

    /**
     * @return MorphTo<Model, $this>
     */
    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Public URL of the file on the `public` disk. Relative to the current
     * host, so images load from the store's own address, not APP_URL.
     */
    public function url(): string
    {
        return asset('storage/'.$this->path);
    }
}
