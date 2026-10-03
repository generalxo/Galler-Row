<?php

namespace App\Models;

use App\Models\Concerns\BelongsToStore;
use Database\Factories\CollectionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * A curated group of artworks within a store, e.g. "Summer 2026" or "Under €500".
 *
 * @property int $id
 * @property int $store_id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property int $position
 * @property bool $is_featured
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['store_id', 'name', 'slug', 'description', 'position', 'is_featured'])]
class Collection extends Model
{
    /** @use HasFactory<CollectionFactory> */
    use BelongsToStore, HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'position' => 'integer',
            'is_featured' => 'boolean',
        ];
    }

    /**
     * @return BelongsToMany<Artwork, $this>
     */
    public function artworks(): BelongsToMany
    {
        return $this->belongsToMany(Artwork::class)
            ->withPivot('position')
            ->orderByPivot('position');
    }
}
