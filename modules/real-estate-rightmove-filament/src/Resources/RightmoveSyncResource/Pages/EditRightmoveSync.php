<?php

declare(strict_types=1);

namespace Liberu\RealEstate\RightmoveFilament\Resources\RightmoveSyncResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\RealEstate\RightmoveFilament\Resources\RightmoveSyncResource;

final class EditRightmoveSync extends EditRecord
{
    protected static string $resource = RightmoveSyncResource::class;
}
