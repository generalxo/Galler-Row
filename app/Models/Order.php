<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Models\Concerns\BelongsToStore;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * A purchase from a single store. Amounts are minor units (cents) in `currency`;
 * addresses are snapshots taken at checkout.
 *
 * @property int $id
 * @property int $store_id
 * @property int|null $user_id
 * @property string $number
 * @property string $email
 * @property OrderStatus $status
 * @property string $currency
 * @property int $subtotal
 * @property int $shipping
 * @property int $tax
 * @property int $total
 * @property array<string, string|null>|null $shipping_address
 * @property array<string, string|null>|null $billing_address
 * @property Carbon|null $placed_at
 * @property Carbon|null $paid_at
 * @property Carbon|null $shipped_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'store_id', 'user_id', 'number', 'email', 'status', 'currency',
    'subtotal', 'shipping', 'tax', 'total',
    'shipping_address', 'billing_address',
    'placed_at', 'paid_at', 'shipped_at',
])]
class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use BelongsToStore, HasFactory;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'pending',
        'shipping' => 0,
        'tax' => 0,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'subtotal' => 'integer',
            'shipping' => 'integer',
            'tax' => 'integer',
            'total' => 'integer',
            'shipping_address' => 'array',
            'billing_address' => 'array',
            'placed_at' => 'datetime',
            'paid_at' => 'datetime',
            'shipped_at' => 'datetime',
        ];
    }

    /**
     * The customer's account; null for guest checkouts or deleted users.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<OrderItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
