<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-neutral-950 text-neutral-100 antialiased">
        <div class="flex min-h-svh flex-col items-center justify-center p-6 md:p-10">
            <div class="flex w-full max-w-sm flex-col gap-6">
                <x-app-logo-icon class="mx-auto size-9" />
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
