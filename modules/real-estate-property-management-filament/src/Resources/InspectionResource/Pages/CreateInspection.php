<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertyManagementFilament\Resources\InspectionResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\PropertyManagement\Application\CreateInspection as CreateInspectionAction;
use Liberu\RealEstate\PropertyManagementFilament\Resources\InspectionResource;

final class CreateInspection extends CreateRecord
{
    protected static string $resource = InspectionResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = auth()->user();
        abort_unless($user?->current_team_id !== null, 403);

        return app(CreateInspectionAction::class)->handle($user->current_team_id, $user->getAuthIdentifier(), $data);
    }
}
