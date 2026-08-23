<?php

declare(strict_types=1);

namespace Liberu\RealEstate\CoreApi;

use Illuminate\Support\ServiceProvider;

final class RealEstateCoreApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
