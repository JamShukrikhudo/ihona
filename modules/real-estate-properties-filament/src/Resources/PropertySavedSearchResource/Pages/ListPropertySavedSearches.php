<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesFilament\Resources\PropertySavedSearchResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\RealEstate\PropertiesFilament\Resources\PropertySavedSearchResource;

final class ListPropertySavedSearches extends ListRecords
{
    protected static string $resource = PropertySavedSearchResource::class;
}
