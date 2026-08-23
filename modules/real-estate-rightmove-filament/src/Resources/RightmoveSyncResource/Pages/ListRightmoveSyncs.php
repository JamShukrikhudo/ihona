<?php

declare(strict_types=1);

namespace Liberu\RealEstate\RightmoveFilament\Resources\RightmoveSyncResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\RealEstate\RightmoveFilament\Resources\RightmoveSyncResource;

final class ListRightmoveSyncs extends ListRecords
{
    protected static string $resource = RightmoveSyncResource::class;
}
