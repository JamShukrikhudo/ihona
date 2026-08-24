<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MarketingLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class MarketingLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'real-estate-marketing-livewire');
        Livewire::component('module-real-estate-marketing::marketing-campaign-list', Components\MarketingCampaignList::class);
    }
}
