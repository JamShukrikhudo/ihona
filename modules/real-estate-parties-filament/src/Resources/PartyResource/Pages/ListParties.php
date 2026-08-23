<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PartiesFilament\Resources\PartyResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\RealEstate\PartiesFilament\Resources\PartyResource;

final class ListParties extends ListRecords
{
    protected static string $resource = PartyResource::class;
}
