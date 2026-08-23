<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesFilament\Resources\PropertyResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\RealEstate\PropertiesFilament\Resources\PropertyResource;

final class EditProperty extends EditRecord
{
    protected static string $resource = PropertyResource::class;
}
