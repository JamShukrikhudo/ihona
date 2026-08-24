<?php

declare(strict_types=1);

namespace Liberu\RealEstate\SalesProgressionFilament\Resources\SalesProgressionResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\SalesProgression\Application\CreateSalesProgression as CreateSalesProgressionAction;
use Liberu\RealEstate\SalesProgressionFilament\Resources\SalesProgressionResource;

final class CreateSalesProgression extends CreateRecord
{
    protected static string $resource = SalesProgressionResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = auth()->user();
        abort_unless($user?->current_team_id !== null, 403);

        return app(CreateSalesProgressionAction::class)->handle($user->current_team_id, $user->getAuthIdentifier(), $data);
    }
}
