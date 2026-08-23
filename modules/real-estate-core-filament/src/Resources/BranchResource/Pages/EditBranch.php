<?php

declare(strict_types=1);

namespace Liberu\RealEstate\CoreFilament\Resources\BranchResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\Core\Application\UpdateBranch as UpdateBranchAction;
use Liberu\RealEstate\CoreFilament\Resources\BranchResource;

final class EditBranch extends EditRecord
{
    protected static string $resource = BranchResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $user = auth()->user();
        abort_unless($user?->current_team_id === $record->team_id, 403);

        return app(UpdateBranchAction::class)->handle($record->team_id, $record->getKey(), $data);
    }
}
