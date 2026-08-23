<?php

declare(strict_types=1);

namespace Liberu\RealEstate\OnTheMarketApi;

use Illuminate\Support\ServiceProvider;

final class OnTheMarketApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
