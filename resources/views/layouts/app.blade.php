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
                    <x-button type="submit" variant="ghost" size="sm" data-test="logout-button">{{ __('Log out') }}</x-button>
                </form>
            @else
                <x-button :href="route('login')" variant="ghost" size="sm">{{ __('Log in') }}</x-button>
            @endauth
        </header>

        <main class="p-6">
            {{ $slot }}
        </main>

        @livewireScripts
    </body>
</html>
