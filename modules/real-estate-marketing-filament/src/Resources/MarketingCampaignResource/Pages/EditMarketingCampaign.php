<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MarketingFilament\Resources\MarketingCampaignResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\Marketing\Application\UpdateMarketingCampaign;
use Liberu\RealEstate\MarketingFilament\Resources\MarketingCampaignResource;

final class EditMarketingCampaign extends EditRecord
{
    protected static string $resource = MarketingCampaignResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null && (string) $teamId === (string) $record->team_id, 403);

        return app(UpdateMarketingCampaign::class)->handle($record, $teamId, $data);
    }
}
