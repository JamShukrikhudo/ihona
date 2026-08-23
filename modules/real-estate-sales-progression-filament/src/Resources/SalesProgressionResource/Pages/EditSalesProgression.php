<?php

declare(strict_types=1);

namespace Liberu\RealEstate\SalesProgressionFilament\Resources\SalesProgressionResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\RealEstate\SalesProgressionFilament\Resources\SalesProgressionResource;

final class EditSalesProgression extends EditRecord
{
    protected static string $resource = SalesProgressionResource::class;
}
