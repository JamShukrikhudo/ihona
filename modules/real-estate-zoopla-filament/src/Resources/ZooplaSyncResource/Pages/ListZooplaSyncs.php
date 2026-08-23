<?php

declare(strict_types=1);

namespace Liberu\RealEstate\ZooplaFilament\Resources\ZooplaSyncResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\RealEstate\ZooplaFilament\Resources\ZooplaSyncResource;

final class ListZooplaSyncs extends ListRecords
{
    protected static string $resource = ZooplaSyncResource::class;
}
