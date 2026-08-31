<?php

declare(strict_types=1);

namespace Liberu\RealEstate\CoreFilament\Resources\AgencyResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\Core\Application\UpdateAgency as UpdateAgencyAction;
use Liberu\RealEstate\CoreFilament\Resources\AgencyResource;

final class EditAgency extends EditRecord
{
    protected static string $resource = AgencyResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null && (string) $teamId === (string) $record->team_id, 403);

        return app(UpdateAgencyAction::class)->handle($teamId, $record->getKey(), $data);
    }
}
