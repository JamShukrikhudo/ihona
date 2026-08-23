<?php

declare(strict_types=1);

namespace Liberu\RealEstate\RightmoveApi;

use Illuminate\Support\ServiceProvider;

final class RightmoveApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
