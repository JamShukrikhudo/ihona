<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Core\Application;

use Illuminate\Support\Facades\DB;
use Liberu\RealEstate\Core\Models\Territory;

final class DeleteTerritory
{
    public function handle(int|string $teamId, int|string $territoryId): void
    {
        DB::transaction(fn (): ?bool => Territory::query()->forTeam($teamId)->findOrFail($territoryId)->delete());
    }
}
