<?php

declare(strict_types=1);

namespace Liberu\RealEstate\SalesProgressionApi;

use Illuminate\Support\ServiceProvider;

final class SalesProgressionApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
