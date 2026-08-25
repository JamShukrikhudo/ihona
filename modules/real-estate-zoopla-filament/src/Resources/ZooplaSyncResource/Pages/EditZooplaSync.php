<?php

declare(strict_types=1);

namespace Liberu\RealEstate\ZooplaFilament\Resources\ZooplaSyncResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\Zoopla\Application\UpdateZooplaSync;
use Liberu\RealEstate\ZooplaFilament\Resources\ZooplaSyncResource;

final class EditZooplaSync extends EditRecord
{
    protected static string $resource = ZooplaSyncResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null && (string) $teamId === (string) $record->team_id, 403);

        return app(UpdateZooplaSync::class)->handle($record, $teamId, $data);
    }
}
