<?php

declare(strict_types=1);

declare(strict_types=1);

namespace Liberu\RealEstate\PropertyManagementFilament\Resources\ManagementRecordResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\PropertyManagement\Application\UpdateManagementRecord as UpdateManagementRecordAction;
use Liberu\RealEstate\PropertyManagementFilament\Resources\ManagementRecordResource;

final class EditManagementRecord extends EditRecord
{
    protected static string $resource = ManagementRecordResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $user = auth()->user();
        abort_unless($user?->current_team_id !== null && (string) $user->current_team_id === (string) $record->team_id, 403);

        return app(UpdateManagementRecordAction::class)->handle($record, $user->current_team_id, $user->getAuthIdentifier(), $data);
    }
}
