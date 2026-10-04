<div>
    <div class="mx-auto max-w-6xl">
        {{-- Hero --}}
        <section class="pt-6 pb-12 md:pt-12">
            <h1 class="type-headline-65 md:type-headline-122">Gallery Row</h1>

            <p class="type-heading-1 mt-8 max-w-2xl">Open your own gallery on the row.</p>

            <p class="type-lead mt-4 max-w-2xl">
                A street of independent galleries, online. Give your work its own storefront, its own address and its own colour. We keep the lights on.
            </p>

            <div class="mt-8 flex flex-wrap items-center gap-6">
                <a href="{{ route('stores.create') }}" class="type-body rounded-sm bg-ink-black px-5 py-3 text-parchment hover:bg-pure-black">
                    Open your store
                </a>
                <a href="#the-row" class="type-body underline underline-offset-4 hover:no-underline">
                    Walk the row
                </a>
            </div>
        </section>

        {{-- The row: every active store as a shopfront on one street --}}
        <section id="the-row" class="scroll-mt-6 pb-16" aria-labelledby="the-row-heading">
            <h2 id="the-row-heading" class="type-heading-2">Walk the row</h2>
            <p class="type-body mt-2 max-w-2xl">These stores are open now. Each one runs on its own address.</p>

            <ul class="mt-8 grid gap-4 border-b-4 border-ink-black pb-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($stores as $store)
                    <li class="flex flex-col">
                        <a href="{{ $store->url() }}" class="group flex h-full flex-col outline-offset-4">
                            <span class="type-headline-32 block truncate rounded-t-sm bg-ink-black px-4 pt-4 pb-3 text-parchment">
                                {{ $store->name }}
                            </span>
                            <span class="flex grow flex-col border-x-2 border-ink-black bg-parchment px-4 py-5">
                                @if ($store->description)
                                    <span class="type-body-sm line-clamp-4">{{ $store->description }}</span>
                                @endif
                                <span class="type-body mt-auto block pt-6 break-all underline underline-offset-4 group-hover:no-underline">
                                    {{ parse_url($store->url(), PHP_URL_HOST) }}
                                </span>
                            </span>
                        </a>
                    </li>
                @endforeach

                <li class="flex flex-col">
                    <a href="{{ route('stores.create') }}" class="group flex h-full flex-col rounded-t-sm border-2 sm:min-h-64 border-dashed border-ink-black outline-offset-4 hover:bg-parchment">
                        <span class="type-headline-32 block px-4 pt-4 pb-3">Your store here</span>
                        <span class="type-body-sm block px-4">This plot is free. Claim an address on the row and hang your first piece.</span>
                        <span class="type-body mt-auto block px-4 pt-6 pb-5 underline underline-offset-4 group-hover:no-underline">
                            Open your store
                        </span>
                    </a>
                </li>
            </ul>
        </section>

        {{-- How it works: a real sequence, so numbered --}}
        <section class="pb-16" aria-labelledby="how-heading">
            <h2 id="how-heading" class="type-heading-2">From empty walls to open doors</h2>

            <ol class="mt-8 grid gap-4 md:grid-cols-3">
                <li class="rounded-sm bg-parchment p-6">
                    <span class="type-headline-43 block" aria-hidden="true">1</span>
                    <h3 class="type-heading-5 mt-4">Claim your address</h3>
                    <p class="type-body mt-2">Your store lives at yourname.gallery-row.com from day one. Already have a domain? Point it at your store instead.</p>
                </li>
                <li class="rounded-sm bg-parchment p-6">
                    <span class="type-headline-43 block" aria-hidden="true">2</span>
                    <h3 class="type-heading-5 mt-4">Hang your work</h3>
                    <p class="type-body mt-2">Add paintings, prints, photographs, sculpture and digital work. Sell one-off originals or limited editions in different sizes and frames.</p>
                </li>
                <li class="rounded-sm bg-parchment p-6">
                    <span class="type-headline-43 block" aria-hidden="true">3</span>
                    <h3 class="type-heading-5 mt-4">Open the doors</h3>
                    <p class="type-body mt-2">Visitors browse your collections and buy straight from your store, in your currency.</p>
                </li>
            </ol>
        </section>

        {{-- What makes it an art store --}}
        <section class="grid gap-8 pb-16 md:grid-cols-[1fr_2fr]" aria-labelledby="art-heading">
            <h2 id="art-heading" class="type-heading-2">Built for art, not for socks</h2>

            <dl class="grid gap-x-8 gap-y-6 sm:grid-cols-2">
                <div class="border-t-2 border-ink-black pt-4">
                    <dt class="type-heading-5">Originals stay original</dt>
                    <dd class="type-body mt-2">An original sells once. An edition knows its size, so the last print of 25 is still number 25, not a one-off.</dd>
                </div>
                <div class="border-t-2 border-ink-black pt-4">
                    <dt class="type-heading-5">Your colours on the door</dt>
                    <dd class="type-body mt-2">Your name, your logo and your accent colour run through every page of your store.</dd>
                </div>
                <div class="border-t-2 border-ink-black pt-4">
                    <dt class="type-heading-5">Your customers are yours</dt>
                    <dd class="type-body mt-2">Artwork, customers and orders stay inside your store. The gallery next door never sees them.</dd>
                </div>
                <div class="border-t-2 border-ink-black pt-4">
                    <dt class="type-heading-5">Artists and collections</dt>
                    <dd class="type-body mt-2">Represent several artists and group their work into collections, then feature the ones you want seen first.</dd>
                </div>
            </dl>
        </section>
    </div>

    {{-- Closing band, bleeding past the layout's padding --}}
    <section class="-mx-6 -mb-6 bg-ink-black px-6 py-16 text-parchment" aria-labelledby="closing-heading">
        <div class="mx-auto max-w-6xl">
            <h2 id="closing-heading" class="type-headline-65 md:type-headline-86">Your wall is waiting.</h2>
            <p class="type-lead mt-6 max-w-2xl">There's room on the row. Open your store and hang your first piece.</p>
            <a href="{{ route('stores.create') }}" class="type-body mt-8 inline-block rounded-sm bg-parchment px-5 py-3 text-ink-black hover:bg-bone-cream focus-visible:outline-parchment">
                Open your store
            </a>
        </div>
    </section>
</div>
