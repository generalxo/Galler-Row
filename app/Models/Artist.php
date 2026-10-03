<?php

namespace App\Models;

use App\Models\Concerns\BelongsToStore;
use Database\Factories\ArtistFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;

/**
 * An artist whose work a store sells. A gallery has many, a solo artist's store one.
 *
 * @property int $id
 * @property int $store_id
 * @property int|null $user_id
 * @property string $name
 * @property string $slug
 * @property string|null $bio
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['store_id', 'user_id', 'name', 'slug', 'bio'])]
class Artist extends Model
{
    /** @use HasFactory<ArtistFactory> */
    use BelongsToStore, HasFactory;

    /**
     * The artist's own account, if they have one.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<Artwork, $this>
     */
    public function artworks(): HasMany
    {
        return $this->hasMany(Artwork::class);
    }

    /**
     * @return MorphMany<Image, $this>
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable')->orderBy('position');
    }
}
