<?php

declare(strict_types=1);

namespace Liberu\RealEstate\OnTheMarketFilament\Resources\OnTheMarketSyncResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\OnTheMarket\Application\CreateOnTheMarketSync as CreateOnTheMarketSyncAction;
use Liberu\RealEstate\OnTheMarketFilament\Resources\OnTheMarketSyncResource;

final class CreateOnTheMarketSync extends CreateRecord
{
    protected static string $resource = OnTheMarketSyncResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = auth()->user();
        abort_unless($user?->current_team_id !== null, 403);

        return app(CreateOnTheMarketSyncAction::class)->handle($user->current_team_id, $user->getAuthIdentifier(), $data);
    }
}
