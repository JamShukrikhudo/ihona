<?php

declare(strict_types=1);

namespace Liberu\RealEstate\SalesProgressionFilament\Resources\SalesProgressionResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\RealEstate\SalesProgressionFilament\Resources\SalesProgressionResource;

final class CreateSalesProgression extends CreateRecord
{
    protected static string $resource = SalesProgressionResource::class;
}
