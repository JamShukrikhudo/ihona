<?php

declare(strict_types=1);

namespace Liberu\RealEstate\CoreFilament\Resources\TerritoryResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\Core\Application\CreateTerritory as CreateTerritoryAction;
use Liberu\RealEstate\CoreFilament\Resources\TerritoryResource;

final class CreateTerritory extends CreateRecord
{
    protected static string $resource = TerritoryResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null, 403);

        return app(CreateTerritoryAction::class)->handle($teamId, $data);
    }
}
