<a {{ $attributes->merge(['class' => 'type-body-lg flex items-center gap-2']) }}>
    <span class="flex size-8 items-center justify-center rounded-xs bg-ink-black text-parchment">
        <x-app-logo-icon class="size-5" />
    </span>
    <span>{{ config('app.name', 'Laravel') }}</span>
</a>
