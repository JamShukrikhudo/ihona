<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Rightmove\Application;

use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Rightmove\Models\RightmoveSync;

final class DeleteRightmoveSync
{
    public function handle(RightmoveSync $sync, int|string $teamId): void
    {
        if ((string) $sync->team_id !== (string) $teamId) {
            throw ValidationException::withMessages(['sync' => 'The Rightmove sync does not belong to this team.']);
        }$sync->delete();
    }
}
