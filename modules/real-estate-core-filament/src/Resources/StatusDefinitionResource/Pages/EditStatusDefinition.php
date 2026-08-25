<?php

declare(strict_types=1);

namespace Liberu\RealEstate\CoreFilament\Resources\StatusDefinitionResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\Core\Application\DefineStatus;
use Liberu\RealEstate\CoreFilament\Resources\StatusDefinitionResource;

final class EditStatusDefinition extends EditRecord
{
    protected static string $resource = StatusDefinitionResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $user = auth()->user();
        abort_unless($user?->current_team_id !== null && (string) $record->team_id === (string) $user->current_team_id, 403);

        return app(DefineStatus::class)->handle($user->current_team_id, $data['entity'], $data['key'], $data['label'], (bool) ($data['active'] ?? true));
    }
}
