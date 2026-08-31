<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertyManagementFilament\Resources\WorkOrderResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\PropertyManagement\Application\CreateWorkOrder as CreateWorkOrderAction;
use Liberu\RealEstate\PropertyManagementFilament\Resources\WorkOrderResource;

final class CreateWorkOrder extends CreateRecord
{
    protected static string $resource = WorkOrderResource::class;
    protected function handleRecordCreation(array $data): Model
    {
        $user = auth()->user();
        abort_unless($user?->current_team_id !== null, 403);
        return app(CreateWorkOrderAction::class)->handle($user->current_team_id, $user->getAuthIdentifier(), $data);
    }
}
