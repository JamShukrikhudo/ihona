<?php

declare(strict_types=1);

namespace Liberu\RealEstate\CoreFilament\Resources\TerritoryResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\RealEstate\CoreFilament\Resources\TerritoryResource;

final class ListTerritories extends ListRecords
{
    protected static string $resource = TerritoryResource::class;
}
