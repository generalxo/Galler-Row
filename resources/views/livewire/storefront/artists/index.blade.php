<div>
    <h1 class="type-headline-65">Artists</h1>

    @if ($artists->isEmpty())
        <p class="type-body mt-6">No artists yet.</p>
    @else
        <ul class="mt-10 grid gap-x-8 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($artists as $artist)
                <li class="border-t border-ink-black py-4">
                    <a href="{{ route('storefront.artists.show', $artist) }}" class="type-heading-5 underline-offset-4 hover:underline">{{ $artist->name }}</a>
                    <p class="type-body-sm mt-1">{{ $artist->artworks_count }} {{ Str::plural('work', $artist->artworks_count) }}</p>
                </li>
            @endforeach
        </ul>
    @endif
</div>
