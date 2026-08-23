<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MatchingFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\RealEstate\MatchingFilament\Resources\MatchProfileResource;

final class MatchingFilamentPlugin implements Plugin
{
    public function getId(): string
    {
        return 'real-estate-matching';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([MatchProfileResource::class]);
    }

    public function boot(Panel $panel): void {}
}
