<?php

declare(strict_types=1);

namespace Liberu\RealEstate\CoreLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class RealEstateCoreLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'real-estate-core-livewire');
        Livewire::addNamespace('module-real-estate-core', classNamespace: __NAMESPACE__.'\\Components');
        Livewire::component('module-real-estate-core::agency-list', Components\AgencyList::class);
        Livewire::component('module-real-estate-core::branch-list', Components\BranchList::class);
        Livewire::component('module-real-estate-core::territory-list', Components\TerritoryList::class);
        Livewire::component('module-real-estate-core::configuration-list', Components\CoreConfigurationList::class);
        Livewire::component('module-real-estate-core::numbering-preview', Components\NumberingPreview::class);
        Livewire::component('real-estate-branches-list', Components\BranchList::class);
    }
}
