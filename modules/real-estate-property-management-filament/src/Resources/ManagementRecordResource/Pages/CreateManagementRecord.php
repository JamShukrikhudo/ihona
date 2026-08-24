<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertyManagementFilament\Resources\ManagementRecordResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\PropertyManagement\Application\CreateManagementRecord as CreateManagementRecordAction;
use Liberu\RealEstate\PropertyManagementFilament\Resources\ManagementRecordResource;

final class CreateManagementRecord extends CreateRecord
{
    protected static string $resource = ManagementRecordResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = auth()->user();
        abort_unless($user?->current_team_id !== null, 403);

        return app(CreateManagementRecordAction::class)->handle($user->current_team_id, $user->getAuthIdentifier(), $data);
    }
}
