<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesLivewire;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class PropertiesLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'real-estate-properties-livewire');
        Livewire::addNamespace('module-real-estate-properties', classNamespace: __NAMESPACE__.'\\Components');
        Livewire::component('module-real-estate-properties::property-list', Components\PropertyList::class);
        Blade::component('property-map', Components\PropertyMap::class);
        Livewire::component('module-real-estate-properties::advanced-property-search', Components\AdvancedPropertySearch::class);
        Livewire::component('module-real-estate-properties::property-detail', Components\PropertyDetail::class);
        Livewire::component('module-real-estate-properties::property-comparison', Components\PropertyComparison::class);
        Livewire::component('module-real-estate-properties::property-tax-estimator', Components\PropertyTaxEstimator::class);
        Livewire::component('module-real-estate-properties::wishlist-manager', Components\WishlistManager::class);
        Livewire::component('module-real-estate-properties::property-review-form', Components\PropertyReviewForm::class);
        Livewire::component('module-real-estate-properties::property-submission-form', Components\PropertySubmissionForm::class);
        Livewire::component('module-real-estate-properties::property-preview', Components\PropertyPreview::class);
        Livewire::component('module-real-estate-properties::neighborhood-review-form', Components\NeighborhoodReviewForm::class);
        Livewire::component('module-real-estate-properties::price-alert-manager', Components\PriceAlertManager::class);
        Livewire::component('price-alert-manager', Components\PriceAlertManager::class);
        Livewire::component('neighborhood-review-form', Components\NeighborhoodReviewForm::class);
        Livewire::component('real-estate-properties-list', Components\PropertyList::class);
    }
}
