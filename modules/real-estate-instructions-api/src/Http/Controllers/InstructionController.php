<?php

declare(strict_types=1);

namespace Liberu\RealEstate\InstructionsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Liberu\RealEstate\Instructions\Application\CreateInstruction;
use Liberu\RealEstate\Instructions\Application\DeleteInstruction;
use Liberu\RealEstate\Instructions\Application\UpdateInstruction;
use Liberu\RealEstate\Instructions\Models\Instruction;

final class InstructionController
{
    public function index(Request $request): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless($teamId !== null, 403);
        $size = max(1, min($request->integer('page_size', 25), 100));

        return response()->json(['data' => Instruction::query()->forTeam($teamId)->latest()->paginate($size)]);
    }

    public function store(Request $request, CreateInstruction $create): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->current_team_id !== null, 403);
        $data = $request->validate(['subject' => ['required', 'string', 'max:255'], 'property_id' => ['nullable', 'integer'], 'party_id' => ['nullable', 'integer'], 'ownership_check' => ['sometimes', 'array'], 'terms' => ['sometimes', 'array'], 'disclosures' => ['sometimes', 'array']]);

        return response()->json(['data' => $create->handle($user->current_team_id, $user->getAuthIdentifier(), $data)], 201);
    }

    public function show(Request $request, Instruction $instruction): JsonResponse
    {
        abort_unless((string) $request->user()?->current_team_id === (string) $instruction->team_id, 404);

        return response()->json(['data' => $instruction]);
    }

    public function update(Request $request, Instruction $instruction, UpdateInstruction $update): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless((string) $teamId === (string) $instruction->team_id, 404);
        $data = $request->validate(['subject' => ['sometimes', 'string', 'max:255'], 'ownership_check' => ['sometimes', 'array'], 'terms' => ['sometimes', 'array'], 'disclosures' => ['sometimes', 'array'], 'status' => ['sometimes', 'string', 'in:draft,pending_approval,approved,withdrawn,rejected'], 'approved_at' => ['nullable', 'date'], 'withdrawn_at' => ['nullable', 'date']]);

        return response()->json(['data' => $update->handle($instruction, $teamId, $data)]);
    }

    public function destroy(Request $request, Instruction $instruction, DeleteInstruction $delete): Response
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless((string) $teamId === (string) $instruction->team_id, 404);
        $delete->handle($instruction, $teamId);

        return response()->noContent();
    }
}
