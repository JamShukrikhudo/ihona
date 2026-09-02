<?php

declare(strict_types=1);

namespace Liberu\RealEstate\VrDesignApi;

use Illuminate\Support\ServiceProvider;

final class VrDesignApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
