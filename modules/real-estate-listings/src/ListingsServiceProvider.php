<?php
declare(strict_types=1);
namespace Liberu\RealEstate\Listings;
use Illuminate\Support\ServiceProvider;
final class ListingsServiceProvider extends ServiceProvider { public function boot():void{$this->loadMigrationsFrom(__DIR__.'/../database/migrations');} }
