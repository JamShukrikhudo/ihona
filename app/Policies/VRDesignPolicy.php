<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VRDesign;

class VRDesignPolicy
{
    public function before(User $user): ?bool
    {
        return $user->hasAnyRole(['admin', 'super_admin']) ? true : null;
    }

    public function view(User $user, VRDesign $design): bool
    {
        return $design->is_public
            || $design->user_id === $user->id
            || ($design->team_id !== null && $user->teams()->whereKey($design->team_id)->exists());
    }

    public function update(User $user, VRDesign $design): bool
    {
        return $design->user_id === $user->id
            || ($design->team_id !== null && $user->teams()->whereKey($design->team_id)->exists());
    }

    public function delete(User $user, VRDesign $design): bool
    {
        return $this->update($user, $design);
    }
}
