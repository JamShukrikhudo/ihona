<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PortalsReportingFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;

final class PortalsReportingFilamentPlugin implements Plugin
{
    public function getId(): string
    {
        return 'real-estate-portals-reporting';
    }

    public function register(Panel $panel): void {}

    public function boot(Panel $panel): void {}
}
