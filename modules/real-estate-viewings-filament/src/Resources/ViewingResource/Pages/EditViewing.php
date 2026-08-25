<?php

namespace Liberu\RealEstate\ViewingsFilament\Resources\ViewingResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\Viewings\Application\UpdateViewing as UpdateViewingAction;
use Liberu\RealEstate\ViewingsFilament\Resources\ViewingResource;

final class EditViewing extends EditRecord
{
    protected static string $resource = ViewingResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null && (string) $teamId === (string) $record->team_id, 403);
        foreach (['access', 'accompaniment', 'reminders', 'feedback'] as $field) {
            if (is_string($data[$field] ?? null)) {
                $data[$field] = json_decode($data[$field], true) ?: [];
            }
        }

        return app(UpdateViewingAction::class)->handle($record, $teamId, $data);
    }
}
