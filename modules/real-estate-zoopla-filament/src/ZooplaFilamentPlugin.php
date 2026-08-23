<?php

declare(strict_types=1);

namespace Liberu\RealEstate\ZooplaFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;

final class ZooplaFilamentPlugin implements Plugin
{
    public function getId(): string
    {
        return 'real-estate-zoopla';
    }

    public function register(Panel $panel): void {}

    public function boot(Panel $panel): void {}
}
