<?php

namespace Liberu\RealEstate\ViewingsFilament\Resources\ViewingResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\Viewings\Application\CreateViewing as CreateViewingAction;
use Liberu\RealEstate\ViewingsFilament\Resources\ViewingResource;

final class CreateViewing extends CreateRecord
{
    protected static string $resource = ViewingResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null, 403);

        foreach (['access', 'accompaniment', 'reminders'] as $field) {
            if (is_string($data[$field] ?? null)) {
                $data[$field] = json_decode($data[$field], true) ?: [];
            }
        }

        return app(CreateViewingAction::class)->handle($teamId, auth()->id(), $data);
    }
}
