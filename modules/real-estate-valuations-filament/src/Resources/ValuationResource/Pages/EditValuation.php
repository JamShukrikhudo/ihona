<?php

namespace Liberu\RealEstate\ValuationsFilament\Resources\ValuationResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\Valuations\Application\UpdateValuation as UpdateValuationAction;
use Liberu\RealEstate\ValuationsFilament\Resources\ValuationResource;

final class EditValuation extends EditRecord
{
    protected static string $resource = ValuationResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null && (string) $teamId === (string) $record->team_id, 403);

        return app(UpdateValuationAction::class)->handle($record, $teamId, $data);
    }
}
