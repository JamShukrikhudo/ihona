<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertyManagementLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class PropertyManagementLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'real-estate-property-management-livewire');
        Livewire::addNamespace('module-real-estate-property-management', classNamespace: __NAMESPACE__.'\\Components');
        Livewire::component('module-real-estate-property-management::management-record-list', Components\ManagementRecordList::class);
        Livewire::component('module-real-estate-property-management::maintenance-request-form', Components\MaintenanceRequestForm::class);
    }
}
