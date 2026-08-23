<?php

declare(strict_types=1);

namespace Liberu\RealEstate\SalesProgressionFilament\Resources\SalesProgressionResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\RealEstate\SalesProgressionFilament\Resources\SalesProgressionResource;

final class ListSalesProgressions extends ListRecords
{
    protected static string $resource = SalesProgressionResource::class;
}
