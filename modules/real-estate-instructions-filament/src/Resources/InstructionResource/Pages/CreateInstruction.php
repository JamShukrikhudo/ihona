<?php

declare(strict_types=1);

namespace Liberu\RealEstate\InstructionsFilament\Resources\InstructionResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\Instructions\Application\CreateInstruction as CreateInstructionAction;
use Liberu\RealEstate\InstructionsFilament\Resources\InstructionResource;

final class CreateInstruction extends CreateRecord
{
    protected static string $resource = InstructionResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = auth()->user();
        abort_unless($user?->current_team_id !== null, 403);

        return app(CreateInstructionAction::class)->handle($user->current_team_id, $user->getAuthIdentifier(), $data);
    }
}
