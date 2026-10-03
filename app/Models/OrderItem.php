<?php

namespace App\Models;

use Database\Factories\OrderItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * A line on an order. Title, variant name and prices are snapshots, so the
 * line stays intact if the artwork is later changed or deleted.
 *
 * @property int $id
 * @property int $order_id
 * @property int|null $artwork_id
 * @property int|null $artwork_variant_id
 * @property string $title
 * @property string|null $variant_name
 * @property int $unit_price
 * @property int $quantity
 * @property int $line_total
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'order_id', 'artwork_id', 'artwork_variant_id',
    'title', 'variant_name', 'unit_price', 'quantity', 'line_total',
])]
class OrderItem extends Model
{
    /** @use HasFactory<OrderItemFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'unit_price' => 'integer',
            'quantity' => 'integer',
            'line_total' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Order, $this>
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * @return BelongsTo<Artwork, $this>
     */
    public function artwork(): BelongsTo
    {
        return $this->belongsTo(Artwork::class)->withTrashed();
    }

    /**
     * @return BelongsTo<ArtworkVariant, $this>
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ArtworkVariant::class, 'artwork_variant_id');
    }
}
