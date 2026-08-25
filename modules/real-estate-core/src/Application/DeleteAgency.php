<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Core\Application;

use Illuminate\Support\Facades\DB;
use Liberu\RealEstate\Core\Models\Agency;

final class DeleteAgency
{
    public function handle(int|string $teamId, int|string $agencyId): void
    {
        DB::transaction(fn (): ?bool => Agency::query()->forTeam($teamId)->findOrFail($agencyId)->delete());
    }
}
