<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MarketingFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\RealEstate\MarketingFilament\Resources\MarketingCampaignResource;

final class MarketingFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'real-estate-marketing';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([MarketingCampaignResource::class]);
    }

    public function boot(Panel $panel): void {}
}
