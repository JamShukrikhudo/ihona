<?php

declare(strict_types=1);

namespace Liberu\RealEstate\OnTheMarketLivewire;

use Illuminate\Support\ServiceProvider;

final class OnTheMarketLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'real-estate-onthemarket-livewire');
    }
}
