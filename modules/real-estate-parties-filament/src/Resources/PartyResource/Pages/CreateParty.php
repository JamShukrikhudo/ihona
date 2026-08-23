<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PartiesFilament\Resources\PartyResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\Parties\Application\CreateParty as CreatePartyAction;
use Liberu\RealEstate\PartiesFilament\Resources\PartyResource;

final class CreateParty extends CreateRecord
{
    protected static string $resource = PartyResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = auth()->user();
        abort_unless($user?->current_team_id !== null, 403);

        return app(CreatePartyAction::class)->handle($user->current_team_id, $user->getAuthIdentifier(), $data);
    }
}
