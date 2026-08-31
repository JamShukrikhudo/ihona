<?php

declare(strict_types=1);

namespace Liberu\RealEstate\VrDesign;

use Illuminate\Support\ServiceProvider;

final class VrDesignServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/vr-design.php', 'vr-design');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    public function register(): void
    {
        $this->app->singleton(Application\VrDesignService::class);
    }
}
