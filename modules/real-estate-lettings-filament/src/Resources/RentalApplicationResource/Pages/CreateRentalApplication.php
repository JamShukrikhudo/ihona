<?php

declare(strict_types=1);

namespace Liberu\RealEstate\LettingsFilament\Resources\RentalApplicationResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\Lettings\Application\CreateRentalApplication as CreateRentalApplicationAction;
use Liberu\RealEstate\LettingsFilament\Resources\RentalApplicationResource;

final class CreateRentalApplication extends CreateRecord
{
    protected static string $resource = RentalApplicationResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = auth()->user();
        abort_unless($user?->current_team_id !== null, 403);

        return app(CreateRentalApplicationAction::class)->handle($user->current_team_id, $user->getAuthIdentifier(), $data);
    }
}
