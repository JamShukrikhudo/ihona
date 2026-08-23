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
        Livewire::component('real-estate-properties-list', Components\PropertyList::class);
    }
}
