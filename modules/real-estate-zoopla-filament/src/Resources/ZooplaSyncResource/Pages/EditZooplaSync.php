<?php

declare(strict_types=1);

namespace Liberu\RealEstate\ZooplaFilament\Resources\ZooplaSyncResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\RealEstate\ZooplaFilament\Resources\ZooplaSyncResource;

final class EditZooplaSync extends EditRecord
{
    protected static string $resource = ZooplaSyncResource::class;
}
