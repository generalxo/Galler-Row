<div>
    <h1 class="type-headline-65">Artworks</h1>

    <div class="mt-8 flex flex-wrap items-end gap-x-6 gap-y-4 border-b border-ink-black pb-6">
        <div class="grid gap-2">
            <label for="type" class="type-body">Type</label>
            <select id="type" wire:model.live="type" class="type-body rounded-sm border border-ink-black bg-parchment px-3 py-2">
                <option value="">All types</option>
                @foreach ($types as $case)
                    <option value="{{ $case->value }}">{{ ucfirst($case->value) }}s</option>
                @endforeach
            </select>
        </div>

        <div class="grid gap-2">
            <label for="kind" class="type-body">Originals or editions</label>
            <select id="kind" wire:model.live="kind" class="type-body rounded-sm border border-ink-black bg-parchment px-3 py-2">
                <option value="">Both</option>
                <option value="originals">Originals</option>
                <option value="editions">Editions</option>
            </select>
        </div>

        <div class="grid gap-2">
            <label for="sort" class="type-body">Sort by</label>
            <select id="sort" wire:model.live="sort" class="type-body rounded-sm border border-ink-black bg-parchment px-3 py-2">
                <option value="newest">Newest</option>
                <option value="price-asc">Price, low to high</option>
                <option value="price-desc">Price, high to low</option>
            </select>
        </div>

        <label class="type-body flex items-center gap-2 py-2">
            <input type="checkbox" wire:model.live="available" class="size-4 rounded-xs border-ink-black accent-accent" />
            Available only
        </label>
    </div>

    <p class="type-body mt-6" aria-live="polite">
        {{ $artworks->total() }} {{ Str::plural('artwork', $artworks->total()) }}
    </p>

    @if ($artworks->isEmpty())
        <p class="type-body mt-4">No artworks match these filters. Choose another type, or turn off Available only.</p>
    @else
        <div class="mt-6 grid gap-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4" wire:loading.class="opacity-60">
            @foreach ($artworks as $artwork)
                <x-artwork-card :artwork="$artwork" :currency="$currentStore->currency" wire:key="artwork-{{ $artwork->id }}" />
            @endforeach
        </div>

        {{ $artworks->links() }}
    @endif
</div>
