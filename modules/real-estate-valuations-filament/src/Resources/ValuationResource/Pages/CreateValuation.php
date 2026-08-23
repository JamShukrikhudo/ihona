<?php

namespace Liberu\RealEstate\ValuationsFilament\Resources\ValuationResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\RealEstate\ValuationsFilament\Resources\ValuationResource;

final class CreateValuation extends CreateRecord
{
    protected static string $resource = ValuationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['team_id'] = auth()->user()->current_team_id;
        $data['created_by'] = auth()->id();

        return $data;
    }
}
