<?php

declare(strict_types=1);

namespace Liberu\RealEstate\RightmoveFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;

final class RightmoveFilamentPlugin implements Plugin
{
    public function getId(): string
    {
        return 'real-estate-rightmove';
    }

    public function register(Panel $panel): void {}

    public function boot(Panel $panel): void {}
}
