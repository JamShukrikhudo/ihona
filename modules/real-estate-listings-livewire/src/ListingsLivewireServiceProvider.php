<?php

declare(strict_types=1);

namespace Liberu\RealEstate\ListingsLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class ListingsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'real-estate-listings-livewire');
        Livewire::component('module-real-estate-listings::listing-list', Components\ListingList::class);
    }
}
