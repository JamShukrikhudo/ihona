<?php

declare(strict_types=1);

namespace Liberu\RealEstate\CoreFilament\Resources\AgencyResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\Core\Application\CreateAgency as CreateAgencyAction;
use Liberu\RealEstate\CoreFilament\Resources\AgencyResource;

final class CreateAgency extends CreateRecord
{
    protected static string $resource = AgencyResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null, 403);

        return app(CreateAgencyAction::class)->handle($teamId, $data);
    }
}
