<?php

namespace App\Services;

use App\Enums\AgencyRole;
use App\Models\Document;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class DocumentAccessService
{
    public function query(User $user, int $teamId): Builder
    {
        $query = Document::query()->where('team_id', $teamId);
        $team = Team::findOrFail($teamId);
        $role = $this->role($user, $team);

        if ($team->user_id === $user->id || $role === AgencyRole::Admin->value) {
            return $query;
        }

        return $query->where(function (Builder $query) use ($user, $role) {
            $query->where('visibility', 'team')
                ->orWhere('user_id', $user->id)
                ->orWhere(function (Builder $query) use ($user, $role) {
                    $query->where('visibility', 'restricted')
                        ->where(function (Builder $query) use ($user, $role) {
                            $query->whereJsonContains('allowed_user_ids', $user->id);
                            if ($role) {
                                $query->orWhereJsonContains('allowed_roles', $role);
                            }
                        });
                });
        });
    }

    private function role(User $user, Team $team): ?string
    {
        return $team->users()->whereKey($user->id)->first()?->membership?->role;
    }
}
