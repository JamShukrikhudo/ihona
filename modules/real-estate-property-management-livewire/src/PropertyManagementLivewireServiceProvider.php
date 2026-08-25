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
        Livewire::component('module-real-estate-property-management::management-record-list', Components\ManagementRecordList::class);
    }
}
