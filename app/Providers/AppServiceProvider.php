<?php

namespace App\Providers;

use App\Enums\ArtworkType;
use App\Models\Artist;
use App\Models\Artwork;
use App\Models\Store;
use App\Models\User;
use App\Support\CurrentStore;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->scoped(CurrentStore::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureMorphMap();
        $this->shareCurrentStore();
    }

    /**
     * Give the storefront layout and pages the current store as `$currentStore`.
     */
    protected function shareCurrentStore(): void
    {
        View::composer(['layouts::store', 'livewire.storefront.*'], function ($view): void {
            $view->with('currentStore', $this->app->make(CurrentStore::class)->get());
        });
    }

    /**
     * Store short, stable keys in polymorphic type columns instead of class names.
     */
    protected function configureMorphMap(): void
    {
        Relation::enforceMorphMap([
            ...ArtworkType::morphMap(),
            'artwork' => Artwork::class,
            'artist' => Artist::class,
            'store' => Store::class,
            'user' => User::class,
        ]);
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
