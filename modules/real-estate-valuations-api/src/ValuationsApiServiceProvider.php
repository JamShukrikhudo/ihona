<?php

declare(strict_types=1);

namespace Liberu\RealEstate\ValuationsApi;

use Illuminate\Support\ServiceProvider;

final class ValuationsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
