<?php

declare(strict_types=1);

namespace Liberu\RealEstate\OnTheMarketFilament\Resources\OnTheMarketSyncResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\RealEstate\OnTheMarketFilament\Resources\OnTheMarketSyncResource;

final class CreateOnTheMarketSync extends CreateRecord
{
    protected static string $resource = OnTheMarketSyncResource::class;
}
