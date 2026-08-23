<?php

declare(strict_types=1);

namespace Liberu\RealEstate\ZooplaApi;

use Illuminate\Support\ServiceProvider;

final class ZooplaApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
