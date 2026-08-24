<?php

declare(strict_types=1);

namespace Liberu\RealEstate\OffersFilament\Resources\OfferResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\Offers\Application\CreateOffer as CreateOfferAction;
use Liberu\RealEstate\OffersFilament\Resources\OfferResource;

final class CreateOffer extends CreateRecord
{
    protected static string $resource = OfferResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(CreateOfferAction::class)->handle(auth()->user()->current_team_id, auth()->id(), $data);
    }
}
