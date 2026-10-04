<x-layouts::auth :title="__('Log in')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Log in to your account')" :description="__('Enter your email and password below to log in')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Email Address -->
            <div class="grid gap-2">
                <label for="email" class="type-body">{{ __('Email address') }}</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="email"
                    placeholder="email@example.com"
                    @class([
                        'type-body rounded-sm border bg-parchment px-3 py-2',
                        'border-error' => $errors->has('email'),
                        'border-ink-black' => ! $errors->has('email'),
                    ])
                />
                @error('email')
                    <p class="type-body-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="grid gap-2">
                <label for="password" class="type-body">{{ __('Password') }}</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    placeholder="{{ __('Password') }}"
                    @class([
                        'type-body rounded-sm border bg-parchment px-3 py-2',
                        'border-error' => $errors->has('password'),
                        'border-ink-black' => ! $errors->has('password'),
                    ])
                />
                @error('password')
                    <p class="type-body-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me -->
            <label class="type-body flex items-center gap-2">
                <input type="checkbox" name="remember" @checked(old('remember')) class="size-4 rounded-xs border-ink-black" />
                {{ __('Remember me') }}
            </label>

            <x-button type="submit" class="w-full" data-test="login-button">{{ __('Log in') }}</x-button>
        </form>
    </div>
</x-layouts::auth>
