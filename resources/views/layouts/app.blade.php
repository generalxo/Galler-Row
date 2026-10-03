<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen">
        <header class="flex items-center justify-between border-b border-charcoal px-6 py-3">
            <x-app-logo href="{{ route('home') }}" />

            @auth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="type-body cursor-pointer rounded-sm px-3 py-2 hover:bg-parchment" data-test="logout-button">
                        {{ __('Log out') }}
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="type-body rounded-sm px-3 py-2 hover:bg-parchment">
                    {{ __('Log in') }}
                </a>
            @endauth
        </header>

        <main class="p-6">
            {{ $slot }}
        </main>

        @livewireScripts
    </body>
</html>
