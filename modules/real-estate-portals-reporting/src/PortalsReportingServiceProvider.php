<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PortalsReporting;

use Illuminate\Support\ServiceProvider;

final class PortalsReportingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
