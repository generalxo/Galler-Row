<?php

namespace App\Models;

use Database\Factories\ArtworkVariantFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * A purchasable option of an edition (size, framing). When an artwork has
 * variants, their price and stock take precedence over the artwork's own.
 *
 * @property int $id
 * @property int $artwork_id
 * @property string $name
 * @property string|null $sku
 * @property int $price Minor units (cents).
 * @property int $quantity
 * @property int $position
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['artwork_id', 'name', 'sku', 'price', 'quantity', 'position'])]
class ArtworkVariant extends Model
{
    /** @use HasFactory<ArtworkVariantFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'quantity' => 'integer',
            'position' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Artwork, $this>
     */
    public function artwork(): BelongsTo
    {
        return $this->belongsTo(Artwork::class);
    }
}
