<?php

declare(strict_types=1);

namespace Liberu\RealEstate\OnTheMarketFilament\Resources\OnTheMarketSyncResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\RealEstate\OnTheMarketFilament\Resources\OnTheMarketSyncResource;

final class ListOnTheMarketSyncs extends ListRecords
{
    protected static string $resource = OnTheMarketSyncResource::class;
}
