<?php

namespace App\Support;

use App\Models\Store;

/**
 * Holds the store (tenant) the current request is acting on.
 *
 * Bound as a scoped singleton, so it resets between requests and queued jobs.
 * When no store is set, tenant scoping is off (platform admin, console, seeders).
 */
class CurrentStore
{
    protected ?Store $store = null;

    public function set(?Store $store): void
    {
        $this->store = $store;
    }

    public function get(): ?Store
    {
        return $this->store;
    }

    public function id(): ?int
    {
        return $this->store?->id;
    }

    public function has(): bool
    {
        return $this->store !== null;
    }

    public function clear(): void
    {
        $this->store = null;
    }
}
