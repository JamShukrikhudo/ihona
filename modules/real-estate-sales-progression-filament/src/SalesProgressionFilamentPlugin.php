<?php

declare(strict_types=1);

namespace Liberu\RealEstate\SalesProgressionFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;

final class SalesProgressionFilamentPlugin implements Plugin
{
    public function getId(): string
    {
        return 'real-estate-sales-progression';
    }

    public function register(Panel $panel): void {}

    public function boot(Panel $panel): void {}
}
