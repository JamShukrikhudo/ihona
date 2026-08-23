<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PartiesFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\RealEstate\PartiesFilament\Resources\PartyResource;

final class PartiesFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'real-estate-parties';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([PartyResource::class]);
    }

    public function boot(Panel $panel): void {}
}
