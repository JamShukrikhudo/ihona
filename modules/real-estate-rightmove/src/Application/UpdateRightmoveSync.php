<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Rightmove\Application;

use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Rightmove\Models\RightmoveSync;

final class UpdateRightmoveSync
{
    public function handle(RightmoveSync $sync, int|string $teamId, array $attributes): RightmoveSync
    {
        if ((string) $sync->team_id !== (string) $teamId) {
            throw ValidationException::withMessages(['sync' => 'The Rightmove sync does not belong to this team.']);
        }$sync->fill($attributes)->save();

        return $sync->refresh();
    }
}
