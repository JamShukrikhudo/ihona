<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Parties\Application;

use Illuminate\Support\Facades\DB;
use Liberu\RealEstate\Parties\Models\Party;

final class DeleteParty
{
    public function handle(int|string $teamId, int|string $partyId): void
    {
        DB::transaction(fn (): ?bool => Party::query()->forTeam($teamId)->findOrFail($partyId)->delete());
    }
}
