<?php
declare(strict_types=1);
namespace Liberu\RealEstate\ViewingsLivewire;
use Illuminate\Support\ServiceProvider;
final class ViewingsLivewireServiceProvider extends ServiceProvider { public function boot():void{$this->loadViewsFrom(__DIR__.'/../resources/views','real-estate-viewings-livewire');} }
