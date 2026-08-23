<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesFilament\Resources\PropertyResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\Properties\Application\UpdateProperty as UpdatePropertyAction;
use Liberu\RealEstate\PropertiesFilament\Resources\PropertyResource;

final class EditProperty extends EditRecord
{
    protected static string $resource = PropertyResource::class;

    /** @param array<string, mixed> $data */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $user = auth()->user();
        abort_unless($user?->current_team_id === $record->team_id, 403);

        return app(UpdatePropertyAction::class)->handle(
            $record->team_id,
            $user->getAuthIdentifier(),
            $record->getKey(),
            $data,
        );
    }
}
