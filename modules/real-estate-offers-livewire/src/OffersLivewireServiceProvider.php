<?php

declare(strict_types=1);

namespace Liberu\RealEstate\OffersLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class OffersLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'real-estate-offers-livewire');
        Livewire::addNamespace('module-real-estate-offers', classNamespace: __NAMESPACE__.'\\Components');
        Livewire::component('module-real-estate-offers::offer-list', Components\OfferList::class);
    }
}
