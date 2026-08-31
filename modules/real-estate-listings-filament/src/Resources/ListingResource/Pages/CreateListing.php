<?php

declare(strict_types=1);

namespace Liberu\RealEstate\ListingsFilament\Resources\ListingResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\Listings\Application\CreateListing as CreateListingAction;
use Liberu\RealEstate\ListingsFilament\Resources\ListingResource;

final class CreateListing extends CreateRecord
{
    protected static string $resource = ListingResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = auth()->user();
        abort_unless($user?->current_team_id !== null, 403);

        return app(CreateListingAction::class)->handle($user->current_team_id, $user->getAuthIdentifier(), $data);
    }
}
