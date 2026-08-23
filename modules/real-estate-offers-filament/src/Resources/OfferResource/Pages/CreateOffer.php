<?php

namespace Liberu\RealEstate\OffersFilament\Resources\OfferResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\RealEstate\OffersFilament\Resources\OfferResource;

final class CreateOffer extends CreateRecord
{
    protected static string $resource = OfferResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['team_id'] = auth()->user()->current_team_id;
        $data['created_by'] = auth()->id();

        return $data;
    }
}
