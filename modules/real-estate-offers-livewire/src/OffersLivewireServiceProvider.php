<?php
declare(strict_types=1);
namespace Liberu\RealEstate\OffersLivewire;
use Illuminate\Support\ServiceProvider;
final class OffersLivewireServiceProvider extends ServiceProvider { public function boot():void{$this->loadViewsFrom(__DIR__.'/../resources/views','real-estate-offers-livewire');} }
