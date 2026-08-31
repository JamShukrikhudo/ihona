<?php

declare(strict_types=1);

namespace Liberu\RealEstate\ZooplaFilament\Resources\ZooplaSyncResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\Zoopla\Application\CreateZooplaSync as CreateZooplaSyncAction;
use Liberu\RealEstate\ZooplaFilament\Resources\ZooplaSyncResource;

final class CreateZooplaSync extends CreateRecord
{
    protected static string $resource = ZooplaSyncResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = auth()->user();
        abort_unless($user?->current_team_id !== null, 403);

        return app(CreateZooplaSyncAction::class)->handle($user->current_team_id, $user->getAuthIdentifier(), $data);
    }
}
