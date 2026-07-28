<?php

namespace App\Http\Controllers\API\V1;

use App\Enums\AgencyRole;
use App\Http\Controllers\Controller;
use App\Models\AgencyRoleAudit;
use App\Models\Team;
use App\Services\AgencyPermissionService;
use App\Services\AgencyRoleAuditService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    public function __construct(
        private readonly AgencyPermissionService $permissions,
        private readonly AgencyRoleAuditService $roleAudits,
    ) {}

    public function catalog(): JsonResponse
    {
        return response()->json([
            'data' => [
                'resources' => AgencyPermissionService::RESOURCES,
                'actions' => AgencyPermissionService::ACTIONS,
                'permissions' => $this->permissions->catalog(),
                'roles' => collect(AgencyRole::cases())->map(fn (AgencyRole $role) => [
                    'name' => $role->value,
                    'assignable' => in_array($role->value, AgencyRole::assignable(), true),
                    'default_actions' => $role->defaultActions(),
                ]),
            ],
        ]);
    }

    public function members(Request $request): JsonResponse
    {
        $team = Team::findOrFail($request->user()->current_team_id);
        $members = $team->allUsers()->map(function ($user) use ($team) {
            $membership = $team->users->firstWhere('id', $user->id)?->membership;

            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $team->user_id === $user->id ? 'owner' : $membership?->role,
                'permissions' => $team->user_id === $user->id ? ['*'] : ($membership?->permissions ?? null),
                'effective_permissions' => $this->permissions->effectivePermissions($user, $team),
            ];
        })->values();

        return response()->json(['data' => $members]);
    }

    public function update(Request $request, int $member): JsonResponse
    {
        $team = Team::findOrFail($request->user()->current_team_id);
        abort_if($team->user_id === $member, 422, 'The organisation owner permissions cannot be restricted.');
        $actorRole = $this->permissions->roleFor($request->user(), $team);

        if (! $actorRole->canManageMemberships()) {
            throw new AuthorizationException('Only organisation owners and administrators can manage roles.');
        }

        $validated = $request->validate([
            'role' => ['sometimes', Rule::in(AgencyRole::assignable())],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::in($this->permissions->catalog())],
        ]);

        abort_unless($team->users()->whereKey($member)->exists(), 404);
        $subject = $team->users()->whereKey($member)->firstOrFail();
        $oldRole = AgencyRole::tryFrom($subject->membership->role ?? '') ?? AgencyRole::Member;
        $newRole = AgencyRole::tryFrom($validated['role'] ?? $oldRole->value) ?? AgencyRole::Member;

        if (! $actorRole->canManage($oldRole) || ! $actorRole->canManage($newRole)) {
            throw new AuthorizationException('You cannot manage or assign this organisation role.');
        }

        $oldPermissions = $subject->membership->permissions;

        DB::transaction(function () use ($request, $team, $member, $validated, $oldRole, $newRole, $oldPermissions): void {
            $team->users()->updateExistingPivot($member, $validated);
            $this->roleAudits->record(
                $request,
                $team,
                $member,
                'membership_updated',
                $oldRole->value,
                $newRole->value,
                $oldPermissions,
                array_key_exists('permissions', $validated) ? $validated['permissions'] : $oldPermissions,
            );
        });
        $membership = $team->users()->whereKey($member)->firstOrFail()->membership;

        return response()->json(['data' => [
            'user_id' => $member,
            'role' => $membership->role,
            'permissions' => $membership->permissions,
            'effective_permissions' => $this->permissions->effectivePermissions($subject->fresh(), $team),
        ]]);
    }

    public function audits(Request $request): JsonResponse
    {
        $team = Team::findOrFail($request->user()->current_team_id);

        return response()->json(AgencyRoleAudit::where('team_id', $team->id)
            ->with(['actor:id,name,email', 'subject:id,name,email'])
            ->when($request->filled('subject_id'), fn ($query) => $query->where('subject_id', $request->integer('subject_id')))
            ->latest('id')
            ->paginate(min(max($request->integer('per_page', 20), 1), 100)));
    }
}
