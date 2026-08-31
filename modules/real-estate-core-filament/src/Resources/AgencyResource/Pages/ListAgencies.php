<?php

declare(strict_types=1);

namespace Liberu\RealEstate\CoreFilament\Resources\AgencyResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\RealEstate\CoreFilament\Resources\AgencyResource;

final class ListAgencies extends ListRecords
{
    protected static string $resource = AgencyResource::class;
}
