<?php

namespace App\Models;

use App\Enums\StoreApplicationStatus;
use Database\Factories\StoreApplicationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * A request to open a store, sent from the platform's "Open your store" form.
 * A platform admin approves it into a store, or rejects it.
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $store_name
 * @property string $subdomain
 * @property string|null $website
 * @property string $message
 * @property StoreApplicationStatus $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'email', 'store_name', 'subdomain', 'website', 'message', 'status'])]
class StoreApplication extends Model
{
    /** @use HasFactory<StoreApplicationFactory> */
    use HasFactory;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'pending',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => StoreApplicationStatus::class,
        ];
    }
}
