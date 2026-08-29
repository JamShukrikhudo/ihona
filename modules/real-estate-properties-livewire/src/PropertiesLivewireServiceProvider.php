<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class PropertiesLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'real-estate-properties-livewire');
        Livewire::component('module-real-estate-properties::property-list', Components\PropertyList::class);
        Livewire::component('module-real-estate-properties::advanced-property-search', Components\AdvancedPropertySearch::class);
        Livewire::component('module-real-estate-properties::property-detail', Components\PropertyDetail::class);
        Livewire::component('real-estate-properties-list', Components\PropertyList::class);
    }
}
