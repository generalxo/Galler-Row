<div>
    <a href="{{ route('storefront.collections.index') }}" class="type-body underline underline-offset-4 hover:no-underline">All collections</a>

    <h1 class="type-headline-65 mt-6">{{ $collection->name }}</h1>
    @if ($collection->description)
        <p class="type-lead mt-4 max-w-2xl">{{ $collection->description }}</p>
    @endif

    @if ($artworks->isEmpty())
        <p class="type-body mt-8">Nothing in this collection right now.</p>
    @else
        <div class="mt-10 grid gap-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach ($artworks as $artwork)
                <x-artwork-card :artwork="$artwork" :currency="$currentStore->currency" />
            @endforeach
        </div>
    @endif
</div>
