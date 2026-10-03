@props([
    'status',
])

@if ($status)
    <div {{ $attributes->merge(['class' => 'type-body rounded-xs border border-success bg-success-bg px-4 py-3 text-ink-black']) }}>
        {{ $status }}
    </div>
@endif
