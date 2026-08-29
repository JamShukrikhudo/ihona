<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Properties;

use Illuminate\Support\ServiceProvider;

final class PropertiesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Application\CreateProperty::class);
        $this->app->singleton(Application\UpsertPropertyUnit::class);
        $this->app->singleton(Application\RecordPropertyKey::class);
        $this->app->singleton(Application\TogglePropertyFavorite::class);
        $this->app->singleton(Application\RemovePropertyFavorite::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
