<?php

declare(strict_types=1);

namespace Liberu\RealEstate\OnTheMarket;

use Illuminate\Support\ServiceProvider;

final class OnTheMarketServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->mergeConfigFrom(__DIR__.'/../config/onthemarket.php', 'onthemarket');
    }
}
