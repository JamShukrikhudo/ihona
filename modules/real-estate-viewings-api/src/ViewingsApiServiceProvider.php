<?php
declare(strict_types=1);
namespace Liberu\RealEstate\ViewingsApi;
use Illuminate\Support\ServiceProvider;
final class ViewingsApiServiceProvider extends ServiceProvider { public function boot():void{$this->loadRoutesFrom(__DIR__.'/../routes/api.php');} }
