<?php

declare(strict_types=1);

namespace Liberu\RealEstate\ZooplaFilament\Resources\ZooplaSyncResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\RealEstate\ZooplaFilament\Resources\ZooplaSyncResource;

final class CreateZooplaSync extends CreateRecord
{
    protected static string $resource = ZooplaSyncResource::class;
}
