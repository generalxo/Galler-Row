<?php

namespace App\Models;

use App\Enums\StoreRole;
use App\Enums\StoreStatus;
use App\Support\Color;
use Database\Factories\StoreFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * A tenant: one artist's or gallery's shop on the platform.
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $domain
 * @property string|null $description
 * @property StoreStatus $status
 * @property string $currency
 * @property string|null $accent_color
 * @property string|null $on_accent_color
 * @property string|null $contact_email
 * @property array<string, mixed>|null $settings
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
#[Fillable(['name', 'slug', 'domain', 'description', 'status', 'currency', 'accent_color', 'on_accent_color', 'contact_email', 'settings'])]
class Store extends Model
{
    /** @use HasFactory<StoreFactory> */
    use HasFactory, SoftDeletes;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'pending',
        'currency' => 'EUR',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => StoreStatus::class,
            'settings' => 'array',
        ];
    }

    /**
     * @return BelongsToMany<User, $this, StoreMembership, 'membership'>
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->using(StoreMembership::class)
            ->as('membership')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<User, $this, StoreMembership, 'membership'>
     */
    public function owners(): BelongsToMany
    {
        return $this->members()->wherePivot('role', StoreRole::Owner->value);
    }

    /**
     * @return HasMany<Artist, $this>
     */
    public function artists(): HasMany
    {
        return $this->hasMany(Artist::class);
    }

    /**
     * @return HasMany<Collection, $this>
     */
    public function collections(): HasMany
    {
        return $this->hasMany(Collection::class);
    }

    /**
     * @return HasMany<Artwork, $this>
     */
    public function artworks(): HasMany
    {
        return $this->hasMany(Artwork::class);
    }

    /**
     * @return HasMany<Order, $this>
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Logo, banner and gallery images, told apart by their `collection` column.
     *
     * @return MorphMany<Image, $this>
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable')->orderBy('position');
    }

    /**
     * Find the store a request host belongs to: a subdomain of the root
     * domain by slug, any other host by custom domain.
     */
    public static function findForHost(string $host): ?self
    {
        $host = strtolower(explode(':', $host)[0]);
        $suffix = '.'.strtolower(config('tenancy.root_domain'));

        if (! str_ends_with($host, $suffix)) {
            return static::query()->where('domain', $host)->first();
        }

        $subdomain = substr($host, 0, -strlen($suffix));

        if (str_contains($subdomain, '.') || in_array($subdomain, config('tenancy.reserved_subdomains'), true)) {
            return null;
        }

        return static::query()->where('slug', $subdomain)->first();
    }

    /**
     * Absolute URL on the store's own host, for links built outside a store
     * request (mail, queued jobs, admin). Scheme and port follow APP_URL.
     */
    public function url(string $path = '/'): string
    {
        $appUrl = (string) config('app.url');
        $scheme = parse_url($appUrl, PHP_URL_SCHEME) ?: 'https';
        $port = parse_url($appUrl, PHP_URL_PORT);

        $host = $this->domain ?? $this->slug.'.'.config('tenancy.root_domain');

        return $scheme.'://'.$host.($port ? ":{$port}" : '').'/'.ltrim($path, '/');
    }

    /**
     * Inline CSS that applies the store's accent to everything inside the
     * storefront. Null (keep Gallery Row's default) unless both colours are
     * plain #rrggbb values, so nothing else can reach the style attribute.
     */
    public function accentStyle(): ?string
    {
        if (! Color::isHex($this->accent_color) || ! Color::isHex($this->on_accent_color)) {
            return null;
        }

        return "--color-accent: {$this->accent_color}; --color-on-accent: {$this->on_accent_color};";
    }

    public function isActive(): bool
    {
        return $this->status === StoreStatus::Active;
    }
}
