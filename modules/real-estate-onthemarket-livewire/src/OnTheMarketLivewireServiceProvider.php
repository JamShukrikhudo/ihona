<?php

declare(strict_types=1);

namespace Liberu\RealEstate\OnTheMarketLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class OnTheMarketLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'real-estate-onthemarket-livewire');
        Livewire::addNamespace('module-real-estate-onthemarket', classNamespace: __NAMESPACE__.'\\Components');
        Livewire::component('module-real-estate-onthemarket::sync-list', Components\OnTheMarketSyncList::class);
    }
}
