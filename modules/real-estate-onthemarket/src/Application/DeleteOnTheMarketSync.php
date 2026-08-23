<?php

declare(strict_types=1);

namespace Liberu\RealEstate\OnTheMarket\Application;

use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\OnTheMarket\Models\OnTheMarketSync;

final class DeleteOnTheMarketSync
{
    public function handle(OnTheMarketSync $sync, int|string $teamId): void
    {
        if ((string) $sync->team_id !== (string) $teamId) {
            throw ValidationException::withMessages(['sync' => 'The OnTheMarket sync does not belong to this team.']);
        }$sync->delete();
    }
}
