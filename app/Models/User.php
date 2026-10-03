<?php

namespace App\Models;

use App\Enums\StoreRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * A platform account. Any user can shop; store roles come from memberships,
 * and `is_admin` marks a Gallery Row platform admin.
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property bool $is_admin
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    /**
     * Stores this user is a member (owner or staff) of.
     *
     * @return BelongsToMany<Store, $this, StoreMembership, 'membership'>
     */
    public function stores(): BelongsToMany
    {
        return $this->belongsToMany(Store::class)
            ->using(StoreMembership::class)
            ->as('membership')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * @return HasMany<Order, $this>
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * @return BelongsToMany<Artwork, $this>
     */
    public function favorites(): BelongsToMany
    {
        return $this->belongsToMany(Artwork::class, 'favorites')->withTimestamps();
    }

    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    public function roleIn(Store $store): ?StoreRole
    {
        return StoreMembership::query()
            ->where('store_id', $store->id)
            ->where('user_id', $this->id)
            ->first()
            ?->role;
    }

    public function ownsStore(Store $store): bool
    {
        return $this->roleIn($store) === StoreRole::Owner;
    }

    public function isMemberOf(Store $store): bool
    {
        return $this->roleIn($store) !== null;
    }
}
