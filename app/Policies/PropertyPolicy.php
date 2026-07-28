<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\User;

class PropertyPolicy
{
    public function before(User $user): ?bool
    {
        return $user->hasAnyRole(['admin', 'super_admin']) ? true : null;
    }

    public function view(User $user, Property $property): bool
    {
        return $this->belongsToPropertyTeam($user, $property);
    }

    public function update(User $user, Property $property): bool
    {
        return $this->belongsToPropertyTeam($user, $property);
    }

    private function belongsToPropertyTeam(User $user, Property $property): bool
    {
        return $property->user_id === $user->id
            || ($property->team_id !== null && $user->teams()->whereKey($property->team_id)->exists());
    }
}
