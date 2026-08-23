<?php

declare(strict_types=1);

namespace Liberu\RealEstate\CoreFilament\Resources\BranchResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\Core\Application\CreateBranch as CreateBranchAction;
use Liberu\RealEstate\CoreFilament\Resources\BranchResource;

final class CreateBranch extends CreateRecord
{
    protected static string $resource = BranchResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = auth()->user();
        abort_unless($user?->current_team_id !== null, 403);

        return app(CreateBranchAction::class)->handle($user->current_team_id, $data);
    }
}
