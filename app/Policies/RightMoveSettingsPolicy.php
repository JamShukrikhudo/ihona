<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\RightMoveSettings;
use Illuminate\Auth\Access\HandlesAuthorization;

class RightMoveSettingsPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:RightMoveSettings');
    }

    public function view(AuthUser $authUser, RightMoveSettings $rightMoveSettings): bool
    {
        return $authUser->can('View:RightMoveSettings');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:RightMoveSettings');
    }

    public function update(AuthUser $authUser, RightMoveSettings $rightMoveSettings): bool
    {
        return $authUser->can('Update:RightMoveSettings');
    }

    public function delete(AuthUser $authUser, RightMoveSettings $rightMoveSettings): bool
    {
        return $authUser->can('Delete:RightMoveSettings');
    }

    public function restore(AuthUser $authUser, RightMoveSettings $rightMoveSettings): bool
    {
        return $authUser->can('Restore:RightMoveSettings');
    }

    public function forceDelete(AuthUser $authUser, RightMoveSettings $rightMoveSettings): bool
    {
        return $authUser->can('ForceDelete:RightMoveSettings');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:RightMoveSettings');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:RightMoveSettings');
    }

    public function replicate(AuthUser $authUser, RightMoveSettings $rightMoveSettings): bool
    {
        return $authUser->can('Replicate:RightMoveSettings');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:RightMoveSettings');
    }

}