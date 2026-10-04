<div>
    <section class="w-full py-6 text-center">
        <h1 class="type-headline-65 md:type-headline-122">{{ $store->name }}</h1>
    </section>

    @if ($store->description)
        <section class="mx-auto max-w-2xl px-4 pb-12">
            <p class="type-lead">{{ $store->description }}</p>
        </section>
    @endif
</div>
