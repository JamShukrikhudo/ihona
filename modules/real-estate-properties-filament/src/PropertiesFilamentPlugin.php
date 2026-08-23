<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\RealEstate\PropertiesFilament\Resources\PropertyResource;

final class PropertiesFilamentPlugin implements Plugin
{
    public static function make(): self { return new self(); }

    public function getId(): string { return 'real-estate-properties'; }

    public function register(Panel $panel): void
    {
        $panel->resources([
            PropertyResource::class,
        ]);
    }

    public function boot(Panel $panel): void {}
}
