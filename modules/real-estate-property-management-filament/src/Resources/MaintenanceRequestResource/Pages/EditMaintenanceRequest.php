<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertyManagementFilament\Resources\MaintenanceRequestResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\PropertyManagement\Application\UpdateMaintenanceRequest as UpdateMaintenanceRequestAction;
use Liberu\RealEstate\PropertyManagementFilament\Resources\MaintenanceRequestResource;

final class EditMaintenanceRequest extends EditRecord
{
    protected static string $resource = MaintenanceRequestResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $user = auth()->user();
        abort_unless($user?->current_team_id !== null && (string) $user->current_team_id === (string) $record->team_id, 403);

        return app(UpdateMaintenanceRequestAction::class)->handle($record, $user->current_team_id, $data);
    }
}
