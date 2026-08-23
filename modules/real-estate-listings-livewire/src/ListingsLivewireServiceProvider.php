<?php

declare(strict_types=1);

namespace Liberu\RealEstate\ListingsLivewire;

use Illuminate\Support\ServiceProvider;

final class ListingsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'real-estate-listings-livewire');
    }
}
