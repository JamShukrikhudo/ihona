<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PortalsReportingLivewire;

use Illuminate\Support\ServiceProvider;

final class PortalsReportingLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'real-estate-portals-reporting-livewire');
    }
}
