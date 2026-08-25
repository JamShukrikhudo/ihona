<?php

declare(strict_types=1);

namespace Liberu\RealEstate\LettingsLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class LettingsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'real-estate-lettings-livewire');
        Livewire::component('module-real-estate-lettings::letting-list', Components\LettingList::class);
    }
}
