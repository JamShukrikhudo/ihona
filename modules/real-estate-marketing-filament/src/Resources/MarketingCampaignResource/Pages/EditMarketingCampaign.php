<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MarketingFilament\Resources\MarketingCampaignResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\RealEstate\MarketingFilament\Resources\MarketingCampaignResource;

final class EditMarketingCampaign extends EditRecord
{
    protected static string $resource = MarketingCampaignResource::class;
}
