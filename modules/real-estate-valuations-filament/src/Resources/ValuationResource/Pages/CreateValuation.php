<?php

namespace Liberu\RealEstate\ValuationsFilament\Resources\ValuationResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\Valuations\Application\CreateValuation as CreateValuationAction;
use Liberu\RealEstate\ValuationsFilament\Resources\ValuationResource;

final class CreateValuation extends CreateRecord
{
    protected static string $resource = ValuationResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null, 403);

        return app(CreateValuationAction::class)->handle($teamId, auth()->id(), $data);
    }
}
