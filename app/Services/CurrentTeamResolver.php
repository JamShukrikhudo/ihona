<?php

namespace App\Services;

use App\Models\Team;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class CurrentTeamResolver
{
    public function resolve(User $user): Team
    {
        $teamId = $user->current_team_id;

        if (! $teamId || ! $user->allTeams()->contains('id', $teamId)) {
            throw ValidationException::withMessages([
                'team' => ['Select an organisation you belong to before using this endpoint.'],
            ]);
        }

        return Team::findOrFail($teamId);
    }
}
