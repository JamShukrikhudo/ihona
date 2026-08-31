<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PortalsReportingLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class PortalsReportingLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'real-estate-portals-reporting-livewire');
        Livewire::addNamespace('module-real-estate-portals-reporting', classNamespace: __NAMESPACE__.'\\Components');
        Livewire::component('module-real-estate-portals-reporting::portal-report-list', Components\PortalReportList::class);
    }
}
