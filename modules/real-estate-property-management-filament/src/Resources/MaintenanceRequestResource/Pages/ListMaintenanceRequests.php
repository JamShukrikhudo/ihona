<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertyManagementFilament\Resources\MaintenanceRequestResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\RealEstate\PropertyManagementFilament\Resources\MaintenanceRequestResource;

final class ListMaintenanceRequests extends ListRecords
{
    protected static string $resource = MaintenanceRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
