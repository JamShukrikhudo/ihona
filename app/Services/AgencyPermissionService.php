<?php

namespace App\Services;

use App\Models\Team;
use App\Models\User;

class AgencyPermissionService
{
    public const RESOURCES = [
        'properties', 'contacts', 'companies', 'buyers', 'tenants', 'branches',
        'communications', 'documents', 'inspections', 'maintenance', 'tasks',
        'offers', 'sales-progressions', 'valuations', 'viewings',
        'tenancy-agreements', 'property-matches', 'automations', 'automation-runs',
        'reports', 'notifications', 'permissions', 'api-tokens', 'setup',
        'portal-integrations', 'portal-sync-runs',
        'accounting-integrations', 'accounting-links', 'accounting-sync-runs',
        'calendar', 'task-comments', 'task-attachments',
        'saved-reports', 'dashboard-layouts',
    ];

    public const ACTIONS = ['view', 'create', 'edit', 'delete', 'export', 'approve', 'publish', 'archive'];

    public function can(User $user, Team $team, string $resource, string $action): bool
    {
        if ($team->user_id === $user->id) {
            return true;
        }

        $membership = $team->users()->whereKey($user->id)->first()?->membership;
        if (! $membership) {
            return false;
        }

        if (in_array($resource, ['notifications', 'api-tokens'], true)) {
            return true;
        }

        if ($membership->role === 'admin') {
            return true;
        }

        $permissions = $membership->permissions;
        if (is_array($permissions)) {
            return in_array('*', $permissions, true)
                || in_array("$resource.*", $permissions, true)
                || in_array("$resource.$action", $permissions, true);
        }

        return match ($membership->role) {
            'editor' => in_array($action, ['view', 'create', 'edit'], true),
            default => $action === 'view',
        };
    }

    public function catalog(): array
    {
        return collect(self::RESOURCES)->flatMap(
            fn (string $resource) => collect(self::ACTIONS)->map(
                fn (string $action) => "$resource.$action"
            )
        )->all();
    }
}
