<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Valuations;

use Illuminate\Support\ServiceProvider;

final class ValuationsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
