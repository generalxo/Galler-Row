@props([
    'title',
    'description',
])

<div class="flex w-full flex-col gap-1 text-center">
    <h1 class="text-xl font-semibold">{{ $title }}</h1>
    <p class="text-sm text-neutral-400">{{ $description }}</p>
</div>
