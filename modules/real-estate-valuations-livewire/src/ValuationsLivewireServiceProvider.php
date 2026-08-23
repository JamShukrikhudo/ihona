<?php
declare(strict_types=1);
namespace Liberu\RealEstate\ValuationsLivewire;
use Illuminate\Support\ServiceProvider;
final class ValuationsLivewireServiceProvider extends ServiceProvider { public function boot():void{$this->loadViewsFrom(__DIR__.'/../resources/views','real-estate-valuations-livewire');} }
