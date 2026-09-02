<?php

declare(strict_types=1);

namespace Liberu\RealEstate\ValuationsLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class ValuationsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'real-estate-valuations-livewire');
        Livewire::addNamespace('module-real-estate-valuations', classNamespace: __NAMESPACE__.'\\Components');
        Livewire::component('module-real-estate-valuations::valuation-list', Components\ValuationList::class);
        Livewire::component('module-real-estate-valuations::property-valuation-estimator', Components\PropertyValuationEstimator::class);
        Livewire::component('module-real-estate-valuations::mortgage-calculator', Components\MortgageCalculator::class);
        Livewire::component('module-real-estate-valuations::rental-yield-calculator', Components\RentalYieldCalculator::class);
        Livewire::component('module-real-estate-valuations::calculators', Components\Calculators::class);
        Livewire::component('calculators', Components\Calculators::class);
        Livewire::component('module-real-estate-valuations::rental-yield-calculator', Components\RentalYieldCalculator::class);
    }
}
