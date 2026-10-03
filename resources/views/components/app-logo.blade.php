<a {{ $attributes->merge(['class' => 'flex items-center gap-2 font-semibold']) }}>
    <span class="flex size-8 items-center justify-center rounded-md bg-white text-neutral-900">
        <x-app-logo-icon class="size-5" />
    </span>
    <span>{{ config('app.name', 'Laravel') }}</span>
</a>
