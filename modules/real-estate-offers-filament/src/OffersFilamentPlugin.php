<?php

declare(strict_types=1);

namespace Liberu\RealEstate\OffersFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\RealEstate\OffersFilament\Resources\OfferResource;

final class OffersFilamentPlugin implements Plugin
{
    public function getId(): string
    {
        return 'real-estate-offers';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([OfferResource::class]);
    }

    public function boot(Panel $panel): void {}
}
