<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesFilament\Resources\PropertyCategoryResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\RealEstate\PropertiesFilament\Resources\PropertyCategoryResource;

final class ListPropertyCategories extends ListRecords
{
    protected static string $resource = PropertyCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
