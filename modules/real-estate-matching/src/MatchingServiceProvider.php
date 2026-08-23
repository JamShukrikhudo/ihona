<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Matching;

use Illuminate\Support\ServiceProvider;

final class MatchingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
