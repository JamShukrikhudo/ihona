<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Services\AgencyPermissionService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ApiTokenController extends Controller
{
    public function __construct(private readonly AgencyPermissionService $permissions) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json(['data' => $request->user()->tokens()
            ->latest()
            ->get(['id', 'name', 'abilities', 'last_used_at', 'expires_at', 'created_at'])]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'abilities' => ['nullable', 'array'],
            'abilities.*' => ['string', Rule::in($this->permissions->catalog())],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ]);
        $team = $request->user()->currentTeam;
        $effectivePermissions = $this->permissions->effectivePermissions($request->user(), $team);
        $abilities = $validated['abilities'] ?? $effectivePermissions;

        if ($effectivePermissions !== ['*']) {
            $unauthorised = array_diff($abilities, $effectivePermissions);

            if ($unauthorised !== []) {
                throw ValidationException::withMessages([
                    'abilities' => ['API tokens cannot be granted permissions the current role does not have.'],
                ]);
            }
        }

        $token = $request->user()->createToken(
            $validated['name'],
            $abilities,
            isset($validated['expires_at']) ? Carbon::parse($validated['expires_at']) : null,
        );

        return response()->json([
            'data' => [
                'id' => $token->accessToken->id,
                'name' => $token->accessToken->name,
                'abilities' => $token->accessToken->abilities,
                'expires_at' => $token->accessToken->expires_at,
                'token' => $token->plainTextToken,
            ],
        ], 201);
    }

    public function destroy(Request $request, int $token): JsonResponse
    {
        $request->user()->tokens()->findOrFail($token)->delete();

        return response()->json(null, 204);
    }
}
