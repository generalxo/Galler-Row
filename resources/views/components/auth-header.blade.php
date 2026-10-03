@props([
    'title',
    'description',
])

<div class="flex w-full flex-col gap-1 text-center">
    <h1 class="type-heading-3">{{ $title }}</h1>
    <p class="type-body">{{ $description }}</p>
</div>
