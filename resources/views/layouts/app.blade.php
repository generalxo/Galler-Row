<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-neutral-900 text-neutral-100 antialiased">
        <header class="flex items-center justify-between border-b border-neutral-800 px-6 py-3">
            <x-app-logo href="{{ route('home') }}" />

            @auth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="cursor-pointer rounded-md px-3 py-1.5 text-sm hover:bg-neutral-800" data-test="logout-button">
                        {{ __('Log out') }}
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="rounded-md px-3 py-1.5 text-sm hover:bg-neutral-800">
                    {{ __('Log in') }}
                </a>
            @endauth
        </header>

        <main class="p-6">
            {{ $slot }}
        </main>
    </body>
</html>
