<?php

namespace App\Models;

use App\Enums\ArtworkStatus;
use App\Enums\ArtworkType;
use App\Exceptions\InvalidArtworkInventory;
use App\Models\Concerns\BelongsToStore;
use Database\Factories\ArtworkFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * A piece of art for sale. Shared columns live here; type-specific columns
 * live on the detail model behind `artworkable` (see ArtworkType).
 *
 * Originals are flagged explicitly with `is_original` and never inferred from
 * stock: an edition with one print left is still an edition.
 *
 * @property int $id
 * @property int $store_id
 * @property int|null $artist_id
 * @property string $artworkable_type
 * @property int $artworkable_id
 * @property string $title
 * @property string $slug
 * @property string|null $description
 * @property int|null $year
 * @property string|null $width_cm
 * @property string|null $height_cm
 * @property string|null $depth_cm
 * @property bool $is_original
 * @property int $price Minor units (cents) in the store's currency.
 * @property int $quantity Units in stock.
 * @property int|null $edition_size Total size of the edition run; null for originals.
 * @property ArtworkStatus $status
 * @property Carbon|null $published_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read ArtworkType $type
 * @property-read PaintingDetail|PrintDetail|PhotographDetail|SculptureDetail|DigitalDetail $artworkable
 */
#[Fillable([
    'store_id', 'artist_id', 'title', 'slug', 'description', 'year',
    'width_cm', 'height_cm', 'depth_cm',
    'is_original', 'price', 'quantity', 'edition_size',
    'status', 'published_at',
])]
class Artwork extends Model
{
    /** @use HasFactory<ArtworkFactory> */
    use BelongsToStore, HasFactory, SoftDeletes;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'is_original' => true,
        'quantity' => 1,
        'status' => 'draft',
    ];

    protected static function booted(): void
    {
        static::saving(function (Artwork $artwork): void {
            $artwork->ensureValidInventory();
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'width_cm' => 'decimal:2',
            'height_cm' => 'decimal:2',
            'depth_cm' => 'decimal:2',
            'is_original' => 'boolean',
            'price' => 'integer',
            'quantity' => 'integer',
            'edition_size' => 'integer',
            'status' => ArtworkStatus::class,
            'published_at' => 'datetime',
        ];
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function artworkable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return BelongsTo<Artist, $this>
     */
    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }

    /**
     * Purchasable options of an edition, e.g. sizes or framing.
     *
     * @return HasMany<ArtworkVariant, $this>
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ArtworkVariant::class)->orderBy('position');
    }

    /**
     * Gallery images; the first by position is the primary image.
     *
     * @return MorphMany<Image, $this>
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable')->orderBy('position');
    }

    /**
     * @return BelongsToMany<Collection, $this>
     */
    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(Collection::class)->withPivot('position');
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    /**
     * @return Attribute<ArtworkType, never>
     */
    protected function type(): Attribute
    {
        return Attribute::get(fn (): ArtworkType => ArtworkType::from($this->artworkable_type));
    }

    /**
     * @param  Builder<Artwork>  $query
     */
    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where('status', ArtworkStatus::Published);
    }

    /**
     * @param  Builder<Artwork>  $query
     */
    #[Scope]
    protected function available(Builder $query): void
    {
        $query->where('quantity', '>', 0);
    }

    /**
     * @param  Builder<Artwork>  $query
     */
    #[Scope]
    protected function originals(Builder $query): void
    {
        $query->where('is_original', true);
    }

    /**
     * @param  Builder<Artwork>  $query
     */
    #[Scope]
    protected function editions(Builder $query): void
    {
        $query->where('is_original', false);
    }

    /**
     * @param  Builder<Artwork>  $query
     */
    #[Scope]
    protected function ofType(Builder $query, ArtworkType $type): void
    {
        $query->where('artworkable_type', $type->value);
    }

    /**
     * @throws InvalidArtworkInventory
     */
    public function ensureValidInventory(): void
    {
        if ($this->is_original) {
            if ($this->quantity > 1) {
                throw InvalidArtworkInventory::originalWithStock($this->quantity);
            }

            if ($this->edition_size !== null) {
                throw InvalidArtworkInventory::originalWithEditionSize();
            }

            return;
        }

        if ($this->edition_size === null) {
            throw InvalidArtworkInventory::editionWithoutSize();
        }

        if ($this->quantity > $this->edition_size) {
            throw InvalidArtworkInventory::stockExceedsEdition($this->quantity, $this->edition_size);
        }
    }
}
