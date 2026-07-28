<?php

namespace App\Http\Middleware;

use App\Models\Team;
use App\Services\AgencyPermissionService;
use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class RequireAgencyPermission
{
    public function __construct(private readonly AgencyPermissionService $permissions) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $teamId = $user?->current_team_id;
        $team = $teamId ? Team::find($teamId) : null;

        if (! $user || ! $team || ! $user->allTeams()->contains('id', $team->id)) {
            return response()->json([
                'message' => 'Select an organisation you belong to first.',
                'errors' => ['team' => ['Select an organisation you belong to first.']],
            ], 422);
        }

        $segments = $request->segments();
        $resource = $segments[2] ?? 'unknown';
        $routeName = (string) $request->route()?->getName();
        $action = match (true) {
            str_ends_with($routeName, '.export') => 'export',
            str_ends_with($routeName, '.decision') => 'approve',
            default => match ($request->method()) {
                'GET', 'HEAD' => 'view',
                'POST' => 'create',
                'PUT', 'PATCH' => 'edit',
                'DELETE' => 'delete',
                default => 'view',
            },
        };

        $permission = "$resource.$action";
        $accessToken = $user->currentAccessToken();
        $tokenKey = $accessToken instanceof PersonalAccessToken ? $accessToken->getKey() : null;
        $isPersonalAccessToken = is_int($tokenKey) || is_string($tokenKey);
        $tokenAllows = ! $isPersonalAccessToken
            || $user->tokenCan('*')
            || $user->tokenCan("$resource.*")
            || $user->tokenCan($permission);

        if (! $tokenAllows || ! $this->permissions->can($user, $team, $resource, $action)) {
            return response()->json([
                'message' => 'This action is not permitted by the organisation role or API token.',
                'permission' => $permission,
            ], 403);
        }

        return $next($request);
    }
}
