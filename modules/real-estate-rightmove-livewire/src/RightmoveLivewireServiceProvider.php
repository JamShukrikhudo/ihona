<?php

declare(strict_types=1);

namespace Liberu\RealEstate\RightmoveLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class RightmoveLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'real-estate-rightmove-livewire');
        Livewire::addNamespace('module-real-estate-rightmove', classNamespace: __NAMESPACE__.'\\Components');
        Livewire::component('module-real-estate-rightmove::sync-list', Components\RightmoveSyncList::class);
    }
}
