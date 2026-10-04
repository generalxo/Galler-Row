<div class="flex flex-col gap-16">
    <section>
        <h1 class="type-headline-65 md:type-headline-112">{{ $currentStore->name }}</h1>
        @if ($currentStore->description)
            <p class="type-lead mt-6 max-w-2xl">{{ $currentStore->description }}</p>
        @endif
        <x-button :href="route('storefront.artworks.index')" color="accent" class="mt-8">Browse all artworks</x-button>
    </section>

    @if ($featured && $featuredArtworks->isNotEmpty())
        <section aria-labelledby="featured-heading">
            <div class="flex flex-wrap items-baseline justify-between gap-4">
                <h2 id="featured-heading" class="type-heading-2">{{ $featured->name }}</h2>
                <a href="{{ route('storefront.collections.show', $featured) }}" class="type-body underline underline-offset-4 hover:no-underline">See the whole collection</a>
            </div>
            @if ($featured->description)
                <p class="type-body mt-2 max-w-2xl">{{ $featured->description }}</p>
            @endif
            <div class="mt-8 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($featuredArtworks as $artwork)
                    <x-artwork-card :artwork="$artwork" :currency="$currentStore->currency" />
                @endforeach
            </div>
        </section>
    @endif

    <section aria-labelledby="latest-heading">
        <div class="flex flex-wrap items-baseline justify-between gap-4">
            <h2 id="latest-heading" class="type-heading-2">Recently added</h2>
            <a href="{{ route('storefront.artworks.index') }}" class="type-body underline underline-offset-4 hover:no-underline">All artworks</a>
        </div>
        @if ($latest->isEmpty())
            <p class="type-body mt-4">Nothing on the walls yet. Check back soon.</p>
        @else
            <div class="mt-8 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($latest as $artwork)
                    <x-artwork-card :artwork="$artwork" :currency="$currentStore->currency" />
                @endforeach
            </div>
        @endif
    </section>

    @if ($artists->isNotEmpty())
        <section aria-labelledby="artists-heading">
            <h2 id="artists-heading" class="type-heading-2">Artists</h2>
            <ul class="mt-6 flex flex-wrap gap-x-6 gap-y-3">
                @foreach ($artists as $artist)
                    <li><a href="{{ route('storefront.artists.show', $artist) }}" class="type-body underline underline-offset-4 hover:no-underline">{{ $artist->name }}</a></li>
                @endforeach
            </ul>
        </section>
    @endif
</div>
