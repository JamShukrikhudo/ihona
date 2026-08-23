<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MarketingFilament\Resources\MarketingCampaignResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\RealEstate\MarketingFilament\Resources\MarketingCampaignResource;

final class ListMarketingCampaigns extends ListRecords
{
    protected static string $resource = MarketingCampaignResource::class;
}
