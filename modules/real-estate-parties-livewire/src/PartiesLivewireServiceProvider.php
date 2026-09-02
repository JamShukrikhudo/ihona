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
        Livewire::addNamespace('module-real-estate-parties', classNamespace: __NAMESPACE__.'\\Components');
        Livewire::component('module-real-estate-parties::party-list', Components\PartyList::class);
        Livewire::component('real-estate-parties-list', Components\PartyList::class);
        Livewire::component('module-real-estate-parties::landlord-review-form', Components\LandlordReviewForm::class);
        Livewire::component('module-real-estate-parties::tenant-review-form', Components\TenantReviewForm::class);
        Livewire::component('landlord-review-form', Components\LandlordReviewForm::class);
        Livewire::component('tenant-review-form', Components\TenantReviewForm::class);
        Livewire::component('module-real-estate-parties::contact-enquiry-form', Components\ContactEnquiryForm::class);
        Livewire::component('contact-enquiry-form', Components\ContactEnquiryForm::class);
    }
}
