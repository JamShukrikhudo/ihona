<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesFilament\Resources\PropertyResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\RealEstate\PropertiesFilament\Resources\PropertyResource;

final class ListProperties extends ListRecords
{
    protected static string $resource = PropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
