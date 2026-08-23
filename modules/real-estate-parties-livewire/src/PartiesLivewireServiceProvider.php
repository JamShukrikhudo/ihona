<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PartiesLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class PartiesLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'real-estate-parties-livewire');
        Livewire::component('real-estate-parties-list', Components\PartyList::class);
    }
}
