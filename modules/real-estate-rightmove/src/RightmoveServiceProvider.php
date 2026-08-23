<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Rightmove;

use Illuminate\Support\ServiceProvider;

final class RightmoveServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
