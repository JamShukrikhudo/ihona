<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Services\AgencyPermissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    public function __construct(private readonly AgencyPermissionService $permissions)
    {
    }

    public function catalog(): JsonResponse
    {
        return response()->json([
            'data' => [
                'resources' => AgencyPermissionService::RESOURCES,
                'actions' => AgencyPermissionService::ACTIONS,
                'permissions' => $this->permissions->catalog(),
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
            ];
        })->values();

        return response()->json(['data' => $members]);
    }

    public function update(Request $request, int $member): JsonResponse
    {
        $team = Team::findOrFail($request->user()->current_team_id);
        abort_unless($team->user_id === $request->user()->id || $request->user()->hasTeamRole($team, 'admin'), 403);
        abort_if($team->user_id === $member, 422, 'The organisation owner permissions cannot be restricted.');

        $validated = $request->validate([
            'role' => ['sometimes', Rule::in(['admin', 'editor', 'member'])],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::in($this->permissions->catalog())],
        ]);

        abort_unless($team->users()->whereKey($member)->exists(), 404);
        $team->users()->updateExistingPivot($member, $validated);
        $membership = $team->users()->whereKey($member)->firstOrFail()->membership;

        return response()->json(['data' => [
            'user_id' => $member,
            'role' => $membership->role,
            'permissions' => $membership->permissions,
        ]]);
    }
}
