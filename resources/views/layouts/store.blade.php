<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    {{-- The store's accent colour flows into every bg-accent / text-accent below. --}}
    <body class="flex min-h-screen flex-col" @if ($style = $currentStore->accentStyle()) style="{{ $style }}" @endif>
        <header class="border-b-4 border-accent">
            <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-x-8 gap-y-3 px-4 py-4 sm:px-6">
                <a href="{{ route('storefront.home') }}" class="type-headline-32">{{ $currentStore->name }}</a>

                <nav aria-label="Store" class="flex flex-wrap items-center gap-x-6 gap-y-2">
                    @foreach ([
                        'storefront.artworks.index' => ['Artworks', 'storefront.artworks.*'],
                        'storefront.collections.index' => ['Collections', 'storefront.collections.*'],
                        'storefront.artists.index' => ['Artists', 'storefront.artists.*'],
                    ] as $route => [$label, $pattern])
                        <a href="{{ route($route) }}"
                            @class([
                                'type-body underline-offset-8',
                                'underline decoration-accent decoration-2' => request()->routeIs($pattern),
                                'hover:underline' => ! request()->routeIs($pattern),
                            ])
                            @if (request()->routeIs($pattern)) aria-current="page" @endif>
                            {{ $label }}
                        </a>
                    @endforeach

                    @auth
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-button type="submit" variant="ghost" size="sm" data-test="logout-button">Log out</x-button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="type-body hover:underline underline-offset-8">Log in</a>
                    @endauth
                </nav>
            </div>
        </header>

        <main class="mx-auto w-full max-w-6xl grow px-4 py-8 sm:px-6">
            {{ $slot }}
        </main>

        <footer class="border-t border-ink-black">
            <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-4 px-4 py-6 sm:px-6">
                <p class="type-body-sm">
                    {{ $currentStore->name }}
                    @if ($currentStore->contact_email)
                        <a href="mailto:{{ $currentStore->contact_email }}" class="ml-2 underline underline-offset-4 hover:no-underline">{{ $currentStore->contact_email }}</a>
                    @endif
                </p>
                <p class="type-body-sm">
                    A store on <a href="{{ route('home') }}" class="underline underline-offset-4 hover:no-underline">Gallery Row</a>
                </p>
            </div>
        </footer>

        @livewireScripts
    </body>
</html>
