<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Core;

use Illuminate\Support\ServiceProvider;

final class RealEstateCoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Application\CreateBranch::class);
        $this->app->singleton(Application\UpdateBranch::class);
        $this->app->singleton(Application\DeleteBranch::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
