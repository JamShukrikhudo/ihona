<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MatchingFilament\Resources\MatchProfileResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\Matching\Application\UpdateMatchProfile as UpdateMatchProfileAction;
use Liberu\RealEstate\MatchingFilament\Resources\MatchProfileResource;

final class EditMatchProfile extends EditRecord
{
    protected static string $resource = MatchProfileResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $user = auth()->user();
        abort_unless($user?->current_team_id !== null && (string) $user->current_team_id === (string) $record->team_id, 403);

        return app(UpdateMatchProfileAction::class)->handle($record, $user->current_team_id, $data);
    }
}
