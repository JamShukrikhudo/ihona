<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PortalsReportingApi;

use Illuminate\Support\ServiceProvider;

final class PortalsReportingApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
