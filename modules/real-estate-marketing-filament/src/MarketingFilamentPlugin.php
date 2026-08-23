<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MarketingFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;

final class MarketingFilamentPlugin implements Plugin
{
    public function getId(): string
    {
        return 'real-estate-marketing';
    }

    public function register(Panel $panel): void {}

    public function boot(Panel $panel): void {}
}
