<?php

declare(strict_types=1);

namespace Liberu\RealEstate\RightmoveFilament\Resources\RightmoveSyncResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\Rightmove\Application\CreateRightmoveSync as CreateRightmoveSyncAction;
use Liberu\RealEstate\RightmoveFilament\Resources\RightmoveSyncResource;

final class CreateRightmoveSync extends CreateRecord
{
    protected static string $resource = RightmoveSyncResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = auth()->user();
        abort_unless($user?->current_team_id !== null, 403);

        return app(CreateRightmoveSyncAction::class)->handle($user->current_team_id, $user->getAuthIdentifier(), $data);
    }
}
