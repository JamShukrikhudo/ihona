<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\OnTheMarketSettings;
use Illuminate\Auth\Access\HandlesAuthorization;

class OnTheMarketSettingsPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:OnTheMarketSettings');
    }

    public function view(AuthUser $authUser, OnTheMarketSettings $onTheMarketSettings): bool
    {
        return $authUser->can('View:OnTheMarketSettings');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:OnTheMarketSettings');
    }

    public function update(AuthUser $authUser, OnTheMarketSettings $onTheMarketSettings): bool
    {
        return $authUser->can('Update:OnTheMarketSettings');
    }

    public function delete(AuthUser $authUser, OnTheMarketSettings $onTheMarketSettings): bool
    {
        return $authUser->can('Delete:OnTheMarketSettings');
    }

    public function restore(AuthUser $authUser, OnTheMarketSettings $onTheMarketSettings): bool
    {
        return $authUser->can('Restore:OnTheMarketSettings');
    }

    public function forceDelete(AuthUser $authUser, OnTheMarketSettings $onTheMarketSettings): bool
    {
        return $authUser->can('ForceDelete:OnTheMarketSettings');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:OnTheMarketSettings');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:OnTheMarketSettings');
    }

    public function replicate(AuthUser $authUser, OnTheMarketSettings $onTheMarketSettings): bool
    {
        return $authUser->can('Replicate:OnTheMarketSettings');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:OnTheMarketSettings');
    }

}