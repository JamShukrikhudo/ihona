<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Core\Application;

use Illuminate\Support\Facades\DB;
use Liberu\RealEstate\Core\Models\Branch;

final class DeleteBranch
{
    public function handle(int|string $teamId, int|string $branchId): void
    {
        DB::transaction(fn (): ?bool => Branch::query()->forTeam($teamId)->findOrFail($branchId)->delete());
    }
}
