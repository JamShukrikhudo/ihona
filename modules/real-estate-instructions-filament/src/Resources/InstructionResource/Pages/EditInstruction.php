<?php

declare(strict_types=1);

namespace Liberu\RealEstate\InstructionsFilament\Resources\InstructionResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\Instructions\Application\UpdateInstruction as UpdateInstructionAction;
use Liberu\RealEstate\InstructionsFilament\Resources\InstructionResource;

final class EditInstruction extends EditRecord
{
    protected static string $resource = InstructionResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $user = auth()->user();
        abort_unless($user?->current_team_id !== null && (string) $user->current_team_id === (string) $record->team_id, 403);

        return app(UpdateInstructionAction::class)->handle($record, $user->current_team_id, $data);
    }
}
