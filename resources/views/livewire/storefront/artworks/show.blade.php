@php
    use App\Enums\ArtworkType;
    use App\Support\Money;

    $type = $artwork->type;
    $detail = $artwork->artworkable;
    $isSold = $artwork->isSoldOut();
    $cm = fn ($value) => $value === null ? null : rtrim(rtrim((string) $value, '0'), '.');
    $dimensions = collect([$cm($artwork->width_cm), $cm($artwork->height_cm), $cm($artwork->depth_cm)])->filter()->implode(' × ');

    $availability = match (true) {
        $isSold => 'Sold',
        $type === ArtworkType::Digital => 'Digital download, available now',
        $artwork->is_original => 'Original, one of one',
        $artwork->quantity === 1 => "Last one left of an edition of {$artwork->edition_size}",
        default => "{$artwork->quantity} of {$artwork->edition_size} left",
    };

    $facts = array_filter(match ($type) {
        ArtworkType::Painting => ['Medium' => $detail->medium, 'Support' => ucfirst($detail->surface), 'Frame' => $detail->is_framed ? 'Framed' : 'Unframed'],
        ArtworkType::Print => ['Technique' => $detail->print_method, 'Paper' => $detail->paper, 'Edition' => $detail->is_numbered ? 'Numbered' : null],
        ArtworkType::Photograph => ['Process' => $detail->print_process, 'Paper' => $detail->paper],
        ArtworkType::Sculpture => ['Material' => $detail->material, 'Weight' => $detail->weight_kg ? $cm($detail->weight_kg).' kg' : null, 'Shipping' => $detail->requires_freight ? 'Ships by freight' : null],
        ArtworkType::Digital => ['Format' => strtoupper($detail->file_format), 'Resolution' => $detail->resolution ? str_replace('x', ' × ', $detail->resolution).' px' : null, 'Licence' => $detail->license->value === 'commercial' ? 'Commercial use' : 'Personal use'],
    });
@endphp

<div>
    <a href="{{ route('storefront.artworks.index') }}" class="type-body underline underline-offset-4 hover:no-underline">All artworks</a>

    <div class="mt-6 grid items-start gap-10 lg:grid-cols-[3fr_2fr]">
        {{-- The work itself --}}
        <figure class="flex flex-col gap-4">
            @forelse ($artwork->images as $image)
                <img src="{{ $image->url() }}" alt="{{ $image->alt }}" @if (! $loop->first) loading="lazy" @endif class="w-full border-2 border-ink-black bg-parchment" />
            @empty
                <div class="aspect-[4/5] w-full bg-parchment"></div>
            @endforelse
        </figure>

        {{-- Wall label: what a gallery pins beside the work --}}
        <aside class="lg:sticky lg:top-6">
            <div class="border-l-8 border-accent bg-parchment p-6 sm:p-8">
                <p class="type-heading-5">
                    @if ($artwork->artist)
                        <a href="{{ route('storefront.artists.show', $artwork->artist) }}" class="underline underline-offset-4 hover:no-underline">{{ $artwork->artist->name }}</a>
                    @else
                        Maker unknown
                    @endif
                </p>

                <h1 class="type-heading-1 mt-3">
                    <span class="italic">{{ $artwork->title }}</span>@if ($artwork->year), {{ $artwork->year }}@endif
                </h1>

                <dl class="type-body mt-6 grid grid-cols-[auto_1fr] gap-x-6 gap-y-2">
                    <dt>Type</dt>
                    <dd>{{ ucfirst($type->value) }}</dd>
                    @foreach ($facts as $label => $value)
                        <dt>{{ $label }}</dt>
                        <dd>{{ $value }}</dd>
                    @endforeach
                    @if ($dimensions)
                        <dt>Size</dt>
                        <dd>{{ $dimensions }} cm</dd>
                    @endif
                </dl>

                <div class="mt-8 border-t-2 border-ink-black pt-6">
                    @if ($isSold)
                        <p class="type-heading-2">Sold</p>
                    @else
                        <p class="type-heading-2">{{ Money::format($artwork->price, $currentStore->currency) }}</p>
                    @endif
                    <p class="type-body mt-2">{{ $isSold ? 'This work has found a home.' : $availability }}</p>
                </div>

                @if (! $isSold && $artwork->variants->isNotEmpty())
                    <table class="type-body mt-6 w-full">
                        <caption class="sr-only">Options</caption>
                        <thead>
                            <tr class="border-b border-ink-black text-left">
                                <th scope="col" class="py-2 font-normal">Option</th>
                                <th scope="col" class="py-2 font-normal">Price</th>
                                <th scope="col" class="py-2 text-right font-normal">In stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($artwork->variants as $variant)
                                <tr class="border-b border-ink-black/20">
                                    <td class="py-2">{{ $variant->name }}</td>
                                    <td class="py-2">{{ Money::format($variant->price, $currentStore->currency) }}</td>
                                    <td class="py-2 text-right">{{ $variant->quantity > 0 ? $variant->quantity : 'Sold out' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            @if ($artwork->description)
                <p class="type-body-sm mt-6 lining-nums">{{ $artwork->description }}</p>
            @endif

            @if ($artwork->collections->isNotEmpty())
                <p class="type-body mt-4">
                    In
                    @foreach ($artwork->collections as $collection)
                        <a href="{{ route('storefront.collections.show', $collection) }}" class="underline underline-offset-4 hover:no-underline">{{ $collection->name }}</a>@if (! $loop->last), @endif
                    @endforeach
                </p>
            @endif
        </aside>
    </div>
</div>
