<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PartiesApi;

use Illuminate\Support\ServiceProvider;

final class PartiesApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
