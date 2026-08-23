<?php
declare(strict_types=1);
namespace Liberu\RealEstate\ListingsApi;
use Illuminate\Support\ServiceProvider;
final class ListingsApiServiceProvider extends ServiceProvider { public function boot():void{$this->loadRoutesFrom(__DIR__.'/../routes/api.php');} }
