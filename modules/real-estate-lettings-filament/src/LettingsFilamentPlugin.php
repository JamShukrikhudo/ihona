<?php

declare(strict_types=1);

namespace Liberu\RealEstate\LettingsFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\RealEstate\LettingsFilament\Resources\LeaseAgreementResource;
use Liberu\RealEstate\LettingsFilament\Resources\LettingResource;
use Liberu\RealEstate\LettingsFilament\Resources\RentalApplicationResource;

final class LettingsFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'real-estate-lettings';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([LettingResource::class, LeaseAgreementResource::class, RentalApplicationResource::class]);
    }

    public function boot(Panel $panel): void {}
}
