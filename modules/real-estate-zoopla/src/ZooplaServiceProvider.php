<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Zoopla;

use Illuminate\Support\ServiceProvider;

final class ZooplaServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->mergeConfigFrom(__DIR__.'/../config/zoopla.php', 'zoopla');
    }
}
