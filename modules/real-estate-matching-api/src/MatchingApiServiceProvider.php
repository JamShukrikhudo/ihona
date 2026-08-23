<?php
declare(strict_types=1);
namespace Liberu\RealEstate\MatchingApi;
use Illuminate\Support\ServiceProvider;
final class MatchingApiServiceProvider extends ServiceProvider { public function boot():void{$this->loadRoutesFrom(__DIR__.'/../routes/api.php');} }
