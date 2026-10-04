<div class="mx-auto grid max-w-6xl gap-12 py-6 md:grid-cols-[2fr_3fr] md:py-12">
    <section>
        <h1 class="type-headline-65">Open your store</h1>

        <p class="type-lead mt-6">
            Tell us about your work and the store you have in mind. We read every application and reply within a few days.
        </p>

        <p class="type-body mt-4">
            Once we approve it, your store opens at the address you pick here. You can add your own domain later.
        </p>

        <p class="type-body mt-4">
            Want to see what a store looks like first?
            <a href="{{ route('home') }}#the-row" class="underline underline-offset-4 hover:no-underline">Walk the row</a>.
        </p>
    </section>

    @if ($submitted)
        <section class="rounded-sm bg-parchment p-6 md:p-8" role="status">
            <h2 class="type-heading-2">Application sent</h2>
            <p class="type-body mt-4">
                Thanks, {{ $name }}. We'll email {{ $email }} about {{ $storeName }} within a few days.
            </p>
            <p class="type-body mt-4">
                We're holding <span class="font-semibold">{{ $subdomain }}.{{ $rootDomain }}</span> for you until then.
            </p>
            <a href="{{ route('home') }}" class="type-body mt-8 inline-block rounded-sm bg-ink-black px-5 py-3 text-parchment hover:bg-pure-black">
                Back to Gallery Row
            </a>
        </section>
    @else
        <form wire:submit="submit" class="flex flex-col gap-6 rounded-sm bg-parchment p-6 md:p-8" novalidate>
            <div class="grid gap-6 sm:grid-cols-2">
                <div class="grid content-start gap-2">
                    <label for="name" class="type-body">Your name</label>
                    <input id="name" type="text" wire:model="name" autocomplete="name" required
                        @class([
                            'type-body rounded-sm border bg-parchment px-3 py-2',
                            'border-error' => $errors->has('name'),
                            'border-ink-black' => ! $errors->has('name'),
                        ]) />
                    @error('name')
                        <p class="type-body-sm text-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid content-start gap-2">
                    <label for="email" class="type-body">Email address</label>
                    <input id="email" type="email" wire:model="email" autocomplete="email" required
                        @class([
                            'type-body rounded-sm border bg-parchment px-3 py-2',
                            'border-error' => $errors->has('email'),
                            'border-ink-black' => ! $errors->has('email'),
                        ]) />
                    @error('email')
                        <p class="type-body-sm text-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid gap-2">
                <label for="storeName" class="type-body">Store name</label>
                <input id="storeName" type="text" wire:model.live.debounce.300ms="storeName" required
                    @class([
                        'type-body rounded-sm border bg-parchment px-3 py-2',
                        'border-error' => $errors->has('storeName'),
                        'border-ink-black' => ! $errors->has('storeName'),
                    ]) />
                @error('storeName')
                    <p class="type-body-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-2">
                <label for="subdomain" class="type-body">Store address</label>
                <div @class([
                    'flex items-stretch overflow-hidden rounded-sm border bg-parchment focus-within:outline-2 focus-within:outline-ink-black',
                    'border-error' => $errors->has('subdomain'),
                    'border-ink-black' => ! $errors->has('subdomain'),
                ])>
                    <input id="subdomain" type="text" wire:model.live.debounce.300ms="subdomain" required
                        autocapitalize="none" spellcheck="false" aria-describedby="subdomain-help"
                        class="type-body min-w-0 grow bg-parchment px-3 py-2 focus:outline-none" />
                    <span class="type-body border-l border-ink-black px-3 py-2 whitespace-nowrap">.{{ $rootDomain }}</span>
                </div>
                @error('subdomain')
                    <p class="type-body-sm text-error">{{ $message }}</p>
                @else
                    <p id="subdomain-help" class="type-body-sm">Lowercase letters, numbers and hyphens.</p>
                @enderror
            </div>

            <div class="grid gap-2">
                <label for="website" class="type-body">Website or portfolio <span class="type-body-sm">(optional)</span></label>
                <input id="website" type="url" wire:model="website" placeholder="https://"
                    @class([
                        'type-body rounded-sm border bg-parchment px-3 py-2',
                        'border-error' => $errors->has('website'),
                        'border-ink-black' => ! $errors->has('website'),
                    ]) />
                @error('website')
                    <p class="type-body-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-2">
                <label for="message" class="type-body">About your work</label>
                <textarea id="message" wire:model="message" rows="5" required aria-describedby="message-help"
                    @class([
                        'type-body rounded-sm border bg-parchment px-3 py-2',
                        'border-error' => $errors->has('message'),
                        'border-ink-black' => ! $errors->has('message'),
                    ])></textarea>
                @error('message')
                    <p class="type-body-sm text-error">{{ $message }}</p>
                @else
                    <p id="message-help" class="type-body-sm">What you make, who it's for, and whether you sell originals, editions or both.</p>
                @enderror
            </div>

            <button type="submit" wire:loading.attr="disabled" class="type-body cursor-pointer self-start rounded-sm bg-ink-black px-5 py-3 text-parchment hover:bg-pure-black disabled:cursor-wait">
                <span wire:loading.remove wire:target="submit">Send application</span>
                <span wire:loading wire:target="submit">Sending…</span>
            </button>
        </form>
    @endif
</div>
