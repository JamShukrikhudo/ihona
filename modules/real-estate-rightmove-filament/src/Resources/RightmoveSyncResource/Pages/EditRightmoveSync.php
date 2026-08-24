<?php

declare(strict_types=1);

declare(strict_types=1);

namespace Liberu\RealEstate\RightmoveFilament\Resources\RightmoveSyncResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\Rightmove\Application\UpdateRightmoveSync;
use Liberu\RealEstate\RightmoveFilament\Resources\RightmoveSyncResource;

final class EditRightmoveSync extends EditRecord
{
    protected static string $resource = RightmoveSyncResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null && (string) $teamId === (string) $record->team_id, 403);

        return app(UpdateRightmoveSync::class)->handle($record, $teamId, $data);
    }
}
