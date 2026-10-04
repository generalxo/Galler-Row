<div>
    <h1 class="type-headline-65">Collections</h1>

    @if ($collections->isEmpty())
        <p class="type-body mt-6">No collections yet.</p>
    @else
        <ul class="mt-10 grid gap-10 md:grid-cols-2">
            @foreach ($collections as $collection)
                <li class="group relative flex flex-col gap-4">
                    <div class="grid grid-cols-3 gap-2">
                        @foreach ($collection->artworks->take(3) as $artwork)
                            @if ($image = $artwork->images->first())
                                <img src="{{ $image->url() }}" alt="" loading="lazy" class="aspect-square w-full object-cover" />
                            @endif
                        @endforeach
                    </div>
                    <div>
                        <h2 class="type-heading-3">
                            <a href="{{ route('storefront.collections.show', $collection) }}" class="after:absolute after:inset-0 group-hover:underline underline-offset-4">{{ $collection->name }}</a>
                        </h2>
                        @if ($collection->description)
                            <p class="type-body mt-2">{{ $collection->description }}</p>
                        @endif
                        <p class="type-body-sm mt-1">{{ $collection->artworks_count }} {{ Str::plural('work', $collection->artworks_count) }}</p>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</div>
