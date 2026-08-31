<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertyManagementFilament\Resources\WorkOrderResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\PropertyManagementFilament\Resources\WorkOrderResource;

final class EditWorkOrder extends EditRecord
{
    protected static string $resource = WorkOrderResource::class;
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $user = auth()->user();
        abort_unless($user?->current_team_id !== null && (string) $user->current_team_id === (string) $record->team_id, 403);
        $record->forceFill($data)->save();
        return $record->refresh();
    }
}
