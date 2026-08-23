<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MarketingLivewire;

use Illuminate\Support\ServiceProvider;

final class MarketingLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'real-estate-marketing-livewire');
    }
}
