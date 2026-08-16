<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ZooplaSettings;
use Illuminate\Auth\Access\HandlesAuthorization;

class ZooplaSettingsPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ZooplaSettings');
    }

    public function view(AuthUser $authUser, ZooplaSettings $zooplaSettings): bool
    {
        return $authUser->can('View:ZooplaSettings');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ZooplaSettings');
    }

    public function update(AuthUser $authUser, ZooplaSettings $zooplaSettings): bool
    {
        return $authUser->can('Update:ZooplaSettings');
    }

    public function delete(AuthUser $authUser, ZooplaSettings $zooplaSettings): bool
    {
        return $authUser->can('Delete:ZooplaSettings');
    }

    public function restore(AuthUser $authUser, ZooplaSettings $zooplaSettings): bool
    {
        return $authUser->can('Restore:ZooplaSettings');
    }

    public function forceDelete(AuthUser $authUser, ZooplaSettings $zooplaSettings): bool
    {
        return $authUser->can('ForceDelete:ZooplaSettings');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ZooplaSettings');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ZooplaSettings');
    }

    public function replicate(AuthUser $authUser, ZooplaSettings $zooplaSettings): bool
    {
        return $authUser->can('Replicate:ZooplaSettings');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ZooplaSettings');
    }

}