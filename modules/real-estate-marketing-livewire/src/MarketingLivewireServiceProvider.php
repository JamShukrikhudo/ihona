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
        Livewire::addNamespace('module-real-estate-marketing', classNamespace: __NAMESPACE__.'\\Components');
        Livewire::component('module-real-estate-marketing::marketing-campaign-list', Components\MarketingCampaignList::class);
        Livewire::component('module-real-estate-marketing::news-article-list', Components\NewsArticleList::class);
        Livewire::component('latest-news', Components\LatestNews::class);
        Livewire::component('news-detail', Components\NewsDetail::class);
    }
}
