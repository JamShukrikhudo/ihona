<?php

declare(strict_types=1);

namespace Liberu\RealEstate\RightmoveFilament\Resources\RightmoveSyncResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\RealEstate\RightmoveFilament\Resources\RightmoveSyncResource;

final class CreateRightmoveSync extends CreateRecord
{
    protected static string $resource = RightmoveSyncResource::class;
}
