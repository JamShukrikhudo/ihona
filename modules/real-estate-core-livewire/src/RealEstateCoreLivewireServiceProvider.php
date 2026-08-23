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
        Livewire::component('real-estate-branches-list', Components\BranchList::class);
    }
}
