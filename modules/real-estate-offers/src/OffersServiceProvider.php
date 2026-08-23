<?php
declare(strict_types=1);
namespace Liberu\RealEstate\Offers;
use Illuminate\Support\ServiceProvider;
final class OffersServiceProvider extends ServiceProvider { public function boot():void{$this->loadMigrationsFrom(__DIR__.'/../database/migrations');} }
