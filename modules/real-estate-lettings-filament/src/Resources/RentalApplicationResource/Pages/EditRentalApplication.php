<?php

declare(strict_types=1);

namespace Liberu\RealEstate\LettingsFilament\Resources\RentalApplicationResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\Lettings\Application\UpdateRentalApplicationScreening;
use Liberu\RealEstate\LettingsFilament\Resources\RentalApplicationResource;

final class EditRentalApplication extends EditRecord
{
    protected static string $resource = RentalApplicationResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $user = auth()->user();
        abort_unless($user?->current_team_id !== null && (string) $user->current_team_id === (string) $record->team_id, 403);

        return app(UpdateRentalApplicationScreening::class)->handle($record, $user->current_team_id, $data);
    }
}
