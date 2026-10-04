<div>
    <a href="{{ route('storefront.artists.index') }}" class="type-body underline underline-offset-4 hover:no-underline">All artists</a>

    <h1 class="type-headline-65 mt-6">{{ $artist->name }}</h1>
    @if ($artist->bio)
        <p class="type-lead mt-4 max-w-2xl">{{ $artist->bio }}</p>
    @endif

    @if ($artworks->isEmpty())
        <p class="type-body mt-8">No work on show right now.</p>
    @else
        <div class="mt-10 grid gap-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach ($artworks as $artwork)
                <x-artwork-card :artwork="$artwork" :currency="$currentStore->currency" />
            @endforeach
        </div>
    @endif
</div>
