<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MarketingFilament\Resources\MarketingCampaignResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\RealEstate\MarketingFilament\Resources\MarketingCampaignResource;

final class CreateMarketingCampaign extends CreateRecord
{
    protected static string $resource = MarketingCampaignResource::class;
}
