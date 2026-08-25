<?php

declare(strict_types=1);

namespace Liberu\RealEstate\ListingsFilament\Resources\ListingResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\Listings\Application\UpdateListing as UpdateListingAction;
use Liberu\RealEstate\ListingsFilament\Resources\ListingResource;

final class EditListing extends EditRecord
{
    protected static string $resource = ListingResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $user = auth()->user();
        abort_unless($user?->current_team_id !== null && (string) $user->current_team_id === (string) $record->team_id, 403);

        return app(UpdateListingAction::class)->handle($record, $user->current_team_id, $data);
    }
}
