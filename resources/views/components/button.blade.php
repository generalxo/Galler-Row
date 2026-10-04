{{--
    Button, or a link styled as one when `href` is given.

    size:       sm | md (default) | lg
    color:      ink (default) | parchment | accent | danger | #rrggbb
    variant:    solid (default) | outline | ghost
    text-color: #rrggbb, only with a custom hex colour (default: ink-black or parchment, whichever reads better)

    Outline and ghost always use ink-black text, so they stay readable on any page colour.
--}}
@props([
    'size' => 'md',
    'color' => 'ink',
    'variant' => 'solid',
    'textColor' => null,
    'href' => null,
])

@php
    use App\Support\Color;

    $custom = Color::isHex($color);
    $color = $custom || in_array($color, ['ink', 'parchment', 'accent', 'danger'], true) ? $color : 'ink';
    $variant = in_array($variant, ['solid', 'outline', 'ghost'], true) ? $variant : 'solid';
    $size = in_array($size, ['sm', 'md', 'lg'], true) ? $size : 'md';

    // Ember Orange on Parchment is only 4.0:1, so solid accent text must be 19px semibold or larger.
    $accentText = $variant === 'solid' && $color === 'accent';

    $sizeClasses = match ($size) {
        'sm' => ($accentText ? 'type-body-lg' : 'type-body').' px-3 py-1.5',
        'md' => ($accentText ? 'type-body-lg' : 'type-body').' px-5 py-3',
        'lg' => 'type-lead px-7 py-4',
    };

    $colorClasses = match ($variant) {
        'solid' => match (true) {
            $custom => 'bg-(--button-bg) text-(--button-fg) hover:brightness-90',
            $color === 'parchment' => 'bg-parchment text-ink-black hover:bg-bone-cream',
            $color === 'accent' => 'bg-accent text-on-accent font-semibold hover:brightness-90',
            $color === 'danger' => 'bg-error text-parchment hover:brightness-90',
            default => 'bg-ink-black text-parchment hover:bg-pure-black',
        },
        'outline' => 'border-2 bg-transparent text-ink-black hover:bg-parchment '.match (true) {
            $custom => 'border-(--button-bg)',
            $color === 'parchment' => 'border-parchment',
            $color === 'accent' => 'border-accent',
            $color === 'danger' => 'border-error',
            default => 'border-ink-black',
        },
        'ghost' => 'bg-transparent text-ink-black hover:bg-parchment',
    };

    $style = $custom
        ? '--button-bg: '.$color.'; --button-fg: '.(Color::isHex($textColor) ? $textColor : Color::readableOn($color)).';'
        : null;

    $attributes = $attributes->class([
        'inline-flex cursor-pointer items-center justify-center gap-2 rounded-sm text-center disabled:cursor-not-allowed disabled:opacity-60',
        $sizeClasses,
        $colorClasses,
    ]);

    if ($style) {
        $attributes = $attributes->merge(['style' => $style]);
    }
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes }}>{{ $slot }}</a>
@else
    <button {{ $attributes->merge(['type' => 'button']) }}>{{ $slot }}</button>
@endif
