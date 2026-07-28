<?php

namespace App\Http\Middleware;

use App\Models\Team;
use App\Services\AgencyPermissionService;
use Closure;
use Illuminate\Http\Request;
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
        $action = str_ends_with((string) $request->route()?->getName(), '.export')
            ? 'export'
            : match ($request->method()) {
                'GET', 'HEAD' => 'view',
                'POST' => 'create',
                'PUT', 'PATCH' => 'edit',
                'DELETE' => 'delete',
                default => 'view',
            };

        if (! $this->permissions->can($user, $team, $resource, $action)) {
            return response()->json([
                'message' => 'This action is not permitted for your organisation role.',
                'permission' => "$resource.$action",
            ], 403);
        }

        return $next($request);
    }
}
