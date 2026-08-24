<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MatchingFilament\Resources\MatchProfileResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\Matching\Application\CreateMatchProfile as CreateMatchProfileAction;
use Liberu\RealEstate\MatchingFilament\Resources\MatchProfileResource;

final class CreateMatchProfile extends CreateRecord
{
    protected static string $resource = MatchProfileResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = auth()->user();
        abort_unless($user?->current_team_id !== null, 403);

        return app(CreateMatchProfileAction::class)->handle($user->current_team_id, $user->getAuthIdentifier(), $data);
    }
}
