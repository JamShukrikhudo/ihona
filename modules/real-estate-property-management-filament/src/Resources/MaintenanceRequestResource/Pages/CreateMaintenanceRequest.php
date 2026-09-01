<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertyManagementFilament\Resources\MaintenanceRequestResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\PropertyManagement\Application\CreateMaintenanceRequest as CreateMaintenanceRequestAction;
use Liberu\RealEstate\PropertyManagementFilament\Resources\MaintenanceRequestResource;

final class CreateMaintenanceRequest extends CreateRecord
{
    protected static string $resource = MaintenanceRequestResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = auth()->user();
        abort_unless($user?->current_team_id !== null, 403);

        return app(CreateMaintenanceRequestAction::class)->handle($user->current_team_id, $user->getAuthIdentifier(), [...$data, 'requested_date' => $data['requested_date'] ?? now()->toDateString()]);
    }
}
