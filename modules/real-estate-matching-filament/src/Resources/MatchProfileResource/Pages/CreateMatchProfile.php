<?php

namespace Liberu\RealEstate\MatchingFilament\Resources\MatchProfileResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\RealEstate\MatchingFilament\Resources\MatchProfileResource;

final class CreateMatchProfile extends CreateRecord
{
    protected static string $resource = MatchProfileResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['team_id'] = auth()->user()->current_team_id;
        $data['created_by'] = auth()->id();

        return $data;
    }
}
