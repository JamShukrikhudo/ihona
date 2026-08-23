<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MediaAndDocumentsApi;

use Illuminate\Support\ServiceProvider;

final class MediaAndDocumentsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
