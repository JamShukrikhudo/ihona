<?php

namespace Liberu\RealEstate\OffersFilament\Resources\OfferResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\RealEstate\OffersFilament\Resources\OfferResource;

final class ListOffers extends ListRecords
{
    protected static string $resource = OfferResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
