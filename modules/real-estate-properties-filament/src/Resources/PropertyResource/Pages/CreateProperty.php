<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesFilament\Resources\PropertyResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\RealEstate\Properties\Application\CreateProperty as CreatePropertyAction;
use Liberu\RealEstate\PropertiesFilament\Resources\PropertyResource;

final class CreateProperty extends CreateRecord
{
    protected static string $resource = PropertyResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $user = auth()->user();
        abort_unless($user?->current_team_id !== null, 403);

        return app(CreatePropertyAction::class)->handle(
            $user->current_team_id,
            $user->getAuthIdentifier(),
            $data,
        );
    }
}
