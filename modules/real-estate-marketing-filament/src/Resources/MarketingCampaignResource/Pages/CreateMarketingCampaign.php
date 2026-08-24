<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MarketingFilament\Resources\MarketingCampaignResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\Marketing\Application\CreateMarketingCampaign as CreateMarketingCampaignAction;
use Liberu\RealEstate\MarketingFilament\Resources\MarketingCampaignResource;

final class CreateMarketingCampaign extends CreateRecord
{
    protected static string $resource = MarketingCampaignResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = auth()->user();
        abort_unless($user?->current_team_id !== null, 403);

        return app(CreateMarketingCampaignAction::class)->handle($user->current_team_id, $user->getAuthIdentifier(), $data);
    }
}
