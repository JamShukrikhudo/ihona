<?php

namespace App\Providers;

use App\Models\Property;
use App\Modules\ModuleManager;
use App\Modules\ModuleServiceProvider;
use App\Observers\PropertyObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // One instance, so the navbar's two renders share the menu tree rather
        // than walking it twice. once() is keyed on the instance, so without
        // this the memoisation never applies.
        $this->app->singleton(\App\Services\MenuService::class);

        // Register the module manager as a singleton
        $this->app->singleton(ModuleManager::class, function ($app) {
            return new ModuleManager();
        });

        // Register the module service provider
        $this->app->register(ModuleServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Property observer for automatic history tracking
        Property::observe(PropertyObserver::class);
    }
}
