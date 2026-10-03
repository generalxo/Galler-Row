<?php

namespace App\Models\Concerns;

use App\Models\Store;
use App\Support\CurrentStore;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Marks a model as tenant data owned by a store.
 *
 * While a current store is set, queries only see that store's rows and new
 * rows are assigned to it automatically.
 */
trait BelongsToStore
{
    public static function bootBelongsToStore(): void
    {
        static::addGlobalScope('store', function (Builder $query): void {
            $storeId = app(CurrentStore::class)->id();

            if ($storeId !== null) {
                $query->where($query->qualifyColumn('store_id'), $storeId);
            }
        });

        static::creating(function (self $model): void {
            if ($model->getAttribute('store_id') === null) {
                $model->setAttribute('store_id', app(CurrentStore::class)->id());
            }
        });
    }

    /**
     * @return BelongsTo<Store, $this>
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
