<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Properties;

use Illuminate\Support\ServiceProvider;

final class PropertiesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Application\CreateProperty::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
