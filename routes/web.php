<?php

use App\Http\Middleware\EnsureStore;
use App\Livewire\Home;
use App\Livewire\OpenStore;
use App\Livewire\Storefront;
use Illuminate\Support\Facades\Route;

// The platform site on the root domain. Registered first so the root host matches it.
Route::domain(config('tenancy.root_domain'))->group(function () {
    Route::livewire('/', Home::class)->name('home');
    Route::livewire('/open-a-store', OpenStore::class)->name('stores.create');
});

// Storefronts, on a store subdomain or custom domain (resolved by IdentifyStore).
Route::middleware(EnsureStore::class)->group(function () {
    Route::livewire('/', Storefront\Home::class)->name('storefront.home');
    Route::livewire('/artworks', Storefront\Artworks\Index::class)->name('storefront.artworks.index');
    Route::livewire('/artworks/{artwork:slug}', Storefront\Artworks\Show::class)->name('storefront.artworks.show');
    Route::livewire('/collections', Storefront\Collections\Index::class)->name('storefront.collections.index');
    Route::livewire('/collections/{collection:slug}', Storefront\Collections\Show::class)->name('storefront.collections.show');
    Route::livewire('/artists', Storefront\Artists\Index::class)->name('storefront.artists.index');
    Route::livewire('/artists/{artist:slug}', Storefront\Artists\Show::class)->name('storefront.artists.show');
});
