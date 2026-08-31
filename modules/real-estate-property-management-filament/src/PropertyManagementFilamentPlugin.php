<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertyManagementFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\RealEstate\PropertyManagementFilament\Resources\InspectionResource;
use Liberu\RealEstate\PropertyManagementFilament\Resources\MaintenanceRequestResource;
use Liberu\RealEstate\PropertyManagementFilament\Resources\ManagementRecordResource;
use Liberu\RealEstate\PropertyManagementFilament\Resources\VendorQuoteResource;
use Liberu\RealEstate\PropertyManagementFilament\Resources\WorkOrderResource;

final class PropertyManagementFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'real-estate-property-management';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([ManagementRecordResource::class, InspectionResource::class, MaintenanceRequestResource::class, VendorQuoteResource::class, WorkOrderResource::class]);
    }

    public function boot(Panel $panel): void {}
}
