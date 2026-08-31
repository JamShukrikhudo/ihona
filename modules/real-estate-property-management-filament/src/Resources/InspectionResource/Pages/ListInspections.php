<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertyManagementFilament\Resources\InspectionResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\RealEstate\PropertyManagementFilament\Resources\InspectionResource;

final class ListInspections extends ListRecords
{
    protected static string $resource = InspectionResource::class;
    protected function getHeaderActions(): array { return [CreateAction::make()]; }
}
