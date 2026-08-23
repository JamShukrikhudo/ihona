<?php

declare(strict_types=1);

namespace Liberu\RealEstate\OnTheMarketFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;

final class OnTheMarketFilamentPlugin implements Plugin
{
    public function getId(): string
    {
        return 'real-estate-onthemarket';
    }

    public function register(Panel $panel): void {}

    public function boot(Panel $panel): void {}
}
