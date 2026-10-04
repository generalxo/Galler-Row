@props(['artwork', 'currency'])

@php
    $image = $artwork->images->first();
    $isSold = $artwork->isSoldOut();
    $isNew = ! $isSold && $artwork->published_at?->isAfter(now()->subDays(14));
@endphp

<article {{ $attributes->merge(['class' => 'group relative flex flex-col bg-parchment shadow-card']) }}>
    <div class="relative aspect-[4/5] overflow-hidden bg-bone-cream">
        @if ($image)
            <img src="{{ $image->url() }}" alt="{{ $image->alt }}" loading="lazy" decoding="async" class="size-full object-cover" />
        @endif

        @if ($isNew)
            <span class="type-body-lg absolute top-3 left-3 rounded-xs bg-accent px-2 py-0.5 font-semibold text-on-accent">New</span>
        @endif
    </div>

    <div class="flex grow flex-col gap-1 p-4">
        <h3 class="type-heading-5">
            <a href="{{ route('storefront.artworks.show', $artwork) }}" class="after:absolute after:inset-0 group-hover:underline underline-offset-4">{{ $artwork->title }}</a>
        </h3>
        <p class="type-body-sm italic">{{ $artwork->artist?->name ?? 'Maker unknown' }}</p>
        <p class="type-body mt-auto pt-2">
            @if ($isSold)
                Sold
            @else
                {{ \App\Support\Money::format($artwork->price, $currency) }}
            @endif
        </p>
    </div>
</article>
