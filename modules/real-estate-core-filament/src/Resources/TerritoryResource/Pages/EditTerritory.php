<?php

declare(strict_types=1);

namespace Liberu\RealEstate\CoreFilament\Resources\TerritoryResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\Core\Application\UpdateTerritory as UpdateTerritoryAction;
use Liberu\RealEstate\CoreFilament\Resources\TerritoryResource;

final class EditTerritory extends EditRecord
{
    protected static string $resource = TerritoryResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null && (string) $teamId === (string) $record->team_id, 403);

        return app(UpdateTerritoryAction::class)->handle($teamId, $record->getKey(), $data);
    }
}
