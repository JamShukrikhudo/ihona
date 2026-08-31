<?php

declare(strict_types=1);

namespace Liberu\RealEstate\ZooplaLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class ZooplaLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'real-estate-zoopla-livewire');
        Livewire::addNamespace('module-real-estate-zoopla', classNamespace: __NAMESPACE__.'\\Components');
        Livewire::component('module-real-estate-zoopla::sync-list', Components\ZooplaSyncList::class);
    }
}
