<?php

declare(strict_types=1);

namespace Liberu\RealEstate\OnTheMarketFilament\Resources\OnTheMarketSyncResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\RealEstate\OnTheMarketFilament\Resources\OnTheMarketSyncResource;

final class EditOnTheMarketSync extends EditRecord
{
    protected static string $resource = OnTheMarketSyncResource::class;
}
