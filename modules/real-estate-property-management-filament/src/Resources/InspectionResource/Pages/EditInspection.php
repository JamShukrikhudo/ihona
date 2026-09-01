<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertyManagementFilament\Resources\InspectionResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\PropertyManagement\Application\UpdateInspection as UpdateInspectionAction;
use Liberu\RealEstate\PropertyManagementFilament\Resources\InspectionResource;

final class EditInspection extends EditRecord
{
    protected static string $resource = InspectionResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $user = auth()->user();
        abort_unless($user?->current_team_id !== null && (string) $user->current_team_id === (string) $record->team_id, 403);

        return app(UpdateInspectionAction::class)->handle($record, $user->current_team_id, $data);
    }
}
