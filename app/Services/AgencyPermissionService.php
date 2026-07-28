<?php

namespace App\Services;

use App\Enums\AgencyRole;
use App\Models\Membership;
use App\Models\Team;
use App\Models\User;

class AgencyPermissionService
{
    public const RESOURCES = [
        'properties', 'contacts', 'companies', 'buyers', 'tenants', 'branches', 'departments', 'staff',
        'communications', 'documents', 'inspections', 'maintenance', 'tasks', 'compliance-items',
        'offers', 'sales-progressions', 'valuations', 'viewings',
        'tenancy-agreements', 'property-matches', 'automations', 'automation-runs',
        'reports', 'notifications', 'permissions', 'api-tokens', 'setup',
        'portal-integrations', 'portal-sync-runs',
        'accounting-integrations', 'accounting-links', 'accounting-sync-runs',
        'calendar', 'task-comments', 'task-attachments',
        'saved-reports', 'dashboard-layouts',
        'marketing-assets', 'email-campaigns',
        'property-media',
        'service-integrations',
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

        $permissions = $membership->permissions;
        if (is_array($permissions)) {
            return in_array('*', $permissions, true)
                || in_array("$resource.*", $permissions, true)
                || in_array("$resource.$action", $permissions, true);
        }

        $role = AgencyRole::tryFrom($membership->role ?? '') ?? AgencyRole::Member;

        if ($this->isRestrictedManagementResource($resource)) {
            return match ($role) {
                AgencyRole::Admin => true,
                AgencyRole::Manager => match ($resource) {
                    'staff' => in_array($action, ['view', 'edit'], true),
                    'branches', 'departments' => in_array($action, $role->defaultActions(), true),
                    default => false,
                },
                default => false,
            };
        }

        $actions = $role->defaultActions();

        return in_array('*', $actions, true) || in_array($action, $actions, true);
    }

    public function catalog(): array
    {
        return collect(self::RESOURCES)->flatMap(
            fn (string $resource) => collect(self::ACTIONS)->map(
                fn (string $action) => "$resource.$action"
            )
        )->all();
    }

    public function roleFor(User $user, Team $team): AgencyRole
    {
        if ($team->user_id === $user->id) {
            return AgencyRole::Owner;
        }

        $role = $team->users()->whereKey($user->id)->first()?->membership?->role;

        return AgencyRole::tryFrom($role ?? '') ?? AgencyRole::Member;
    }

    public function effectivePermissions(User $user, Team $team): array
    {
        if ($team->user_id === $user->id) {
            return ['*'];
        }

        /** @var Membership|null $membership */
        $membership = $team->users()->whereKey($user->id)->first()?->membership;

        if (is_array($membership?->permissions)) {
            return $membership->permissions;
        }

        return collect(self::RESOURCES)->flatMap(function (string $resource) use ($user, $team) {
            return collect(self::ACTIONS)
                ->filter(fn (string $action) => $this->can($user, $team, $resource, $action))
                ->map(fn (string $action) => "$resource.$action");
        })->values()->all();
    }

    private function isRestrictedManagementResource(string $resource): bool
    {
        return in_array($resource, [
            'permissions', 'setup', 'staff', 'branches', 'departments',
            'portal-integrations', 'accounting-integrations', 'service-integrations',
        ], true);
    }
}
