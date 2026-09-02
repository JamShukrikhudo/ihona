<?php

declare(strict_types=1);

namespace Liberu\RealEstate\VrDesignLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class VrDesignLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'real-estate-vr-design-livewire');
        Livewire::addNamespace('module-real-estate-vr-design', classNamespace: __NAMESPACE__.'\\Components');
        Livewire::component('module-real-estate-vr-design::design-studio', Components\DesignStudio::class);
    }
}
