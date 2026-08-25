<?php

declare(strict_types=1);

namespace Liberu\RealEstate\LettingsFilament\Resources\LettingResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\Lettings\Application\UpdateLetting as UpdateLettingAction;
use Liberu\RealEstate\LettingsFilament\Resources\LettingResource;

final class EditLetting extends EditRecord
{
    protected static string $resource = LettingResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $user = auth()->user();
        abort_unless($user?->current_team_id !== null && (string) $user->current_team_id === (string) $record->team_id, 403);

        return app(UpdateLettingAction::class)->handle($record, $user->current_team_id, $user->getAuthIdentifier(), $data);
    }
}
