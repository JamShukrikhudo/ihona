<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PartiesApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Liberu\RealEstate\Parties\Application\CreateParty;
use Liberu\RealEstate\Parties\Application\DeleteParty;
use Liberu\RealEstate\Parties\Application\UpdateParty;
use Liberu\RealEstate\Parties\Models\Party;

final class PartyController
{
    public function index(Request $request): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless($teamId !== null, 403);
        $pageSize = max(1, min($request->integer('page_size', 25), 100));

        return response()->json(['data' => Party::query()->forTeam($teamId)->latest()->paginate($pageSize)]);
    }

    public function store(Request $request, CreateParty $create): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->current_team_id !== null, 403);
        $validated = $request->validate([
            'type' => ['required', 'string', 'in:applicant,buyer,vendor,landlord,tenant,solicitor,contractor'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'metadata' => ['sometimes', 'array'],
            'consent_at' => ['nullable', 'date'],
        ]);

        return response()->json(['data' => $create->handle($user->current_team_id, $user->getAuthIdentifier(), $validated)], 201);
    }

    public function show(Request $request, Party $party): JsonResponse
    {
        abort_unless($request->user()?->current_team_id === $party->team_id, 404);

        return response()->json(['data' => $party]);
    }

    public function update(Request $request, Party $party, UpdateParty $update): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->current_team_id === $party->team_id, 404);
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'metadata' => ['sometimes', 'array'],
            'consent_at' => ['nullable', 'date'],
        ]);

        return response()->json(['data' => $update->handle($party->team_id, $party->getKey(), $validated)]);
    }

    public function destroy(Request $request, Party $party, DeleteParty $delete): Response
    {
        abort_unless($request->user()?->current_team_id === $party->team_id, 404);
        $delete->handle($party->team_id, $party->getKey());

        return response()->noContent();
    }
}
