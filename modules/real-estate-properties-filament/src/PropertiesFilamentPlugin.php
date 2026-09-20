<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\RealEstate\PropertiesFilament\Resources\CityResource;
use Liberu\RealEstate\PropertiesFilament\Resources\DistrictResource;
use Liberu\RealEstate\PropertiesFilament\Resources\PropertyCategoryResource;
use Liberu\RealEstate\PropertiesFilament\Resources\PropertyResource;
use Liberu\RealEstate\PropertiesFilament\Resources\PropertySavedSearchResource;
use Liberu\RealEstate\PropertiesFilament\Resources\PropertyTemplateResource;
use Liberu\RealEstate\PropertiesFilament\Resources\RegionResource;

final class PropertiesFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'real-estate-properties';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            PropertyResource::class,
            PropertyCategoryResource::class,
            PropertyTemplateResource::class,
            PropertySavedSearchResource::class,
            RegionResource::class,
            CityResource::class,
            DistrictResource::class,
        ]);
    }

    public function boot(Panel $panel): void {}
}
