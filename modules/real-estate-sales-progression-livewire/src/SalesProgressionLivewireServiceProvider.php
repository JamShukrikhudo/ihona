<?php

declare(strict_types=1);

namespace Liberu\RealEstate\SalesProgressionLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class SalesProgressionLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'real-estate-sales-progression-livewire');
        Livewire::component('module-real-estate-sales-progression::sales-progression-list', Components\SalesProgressionList::class);
    }
}
