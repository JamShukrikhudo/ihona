<?php

declare(strict_types=1);

declare(strict_types=1);

namespace Liberu\RealEstate\OnTheMarketFilament\Resources\OnTheMarketSyncResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\OnTheMarket\Application\UpdateOnTheMarketSync;
use Liberu\RealEstate\OnTheMarketFilament\Resources\OnTheMarketSyncResource;

final class EditOnTheMarketSync extends EditRecord
{
    protected static string $resource = OnTheMarketSyncResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null && (string) $teamId === (string) $record->team_id, 403);

        return app(UpdateOnTheMarketSync::class)->handle($record, $teamId, $data);
    }
}
