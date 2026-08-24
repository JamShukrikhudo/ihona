<?php

declare(strict_types=1);

declare(strict_types=1);

namespace Liberu\RealEstate\LettingsFilament\Resources\LettingResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\Lettings\Application\CreateLetting as CreateLettingAction;
use Liberu\RealEstate\LettingsFilament\Resources\LettingResource;

final class CreateLetting extends CreateRecord
{
    protected static string $resource = LettingResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = auth()->user();
        abort_unless($user?->current_team_id !== null, 403);

        return app(CreateLettingAction::class)->handle($user->current_team_id, $user->getAuthIdentifier(), $data);
    }
}
