<?php

declare(strict_types=1);

namespace Liberu\RealEstate\InstructionsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Liberu\RealEstate\Instructions\Application\CreateInstruction;
use Liberu\RealEstate\Instructions\Application\DeleteInstruction;
use Liberu\RealEstate\Instructions\Application\TransitionInstruction;
use Liberu\RealEstate\Instructions\Application\UpdateInstruction;
use Liberu\RealEstate\Instructions\Domain\InstructionStatus;
use Liberu\RealEstate\Instructions\Models\Instruction;
use Liberu\RealEstate\InstructionsApi\Http\Resources\InstructionResource;

final class InstructionController
{
    public function index(Request $request): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless($teamId !== null, 403);
        $size = max(1, min($request->integer('page_size', 25), 100));

        return InstructionResource::collection(Instruction::query()->forTeam($teamId)->latest()->paginate($size))->response();
    }

    public function store(Request $request, CreateInstruction $create): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->current_team_id !== null, 403);
        $data = $request->validate(['subject' => ['required', 'string', 'max:255'], 'property_id' => ['nullable', 'integer'], 'party_id' => ['nullable', 'integer'], 'ownership_check' => ['sometimes', 'array'], 'terms' => ['sometimes', 'array'], 'disclosures' => ['sometimes', 'array']]);

        return (new InstructionResource($create->handle($user->current_team_id, $user->getAuthIdentifier(), $data)))->response()->setStatusCode(201);
    }

    public function show(Request $request, Instruction $instruction): JsonResponse
    {
        abort_unless((string) $request->user()?->current_team_id === (string) $instruction->team_id, 404);

        return (new InstructionResource($instruction))->response();
    }

    public function update(Request $request, Instruction $instruction, UpdateInstruction $update): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless((string) $teamId === (string) $instruction->team_id, 404);
        $data = $request->validate(['subject' => ['sometimes', 'string', 'max:255'], 'ownership_check' => ['sometimes', 'array'], 'terms' => ['sometimes', 'array'], 'disclosures' => ['sometimes', 'array']]);

        return (new InstructionResource($update->handle($instruction, $teamId, $data)))->response();
    }

    public function destroy(Request $request, Instruction $instruction, DeleteInstruction $delete): Response
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless((string) $teamId === (string) $instruction->team_id, 404);
        $delete->handle($instruction, $teamId);

        return response()->noContent();
    }

    public function transition(Request $request, Instruction $instruction, string $status, TransitionInstruction $transition): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->current_team_id !== null && (string) $user->current_team_id === (string) $instruction->team_id, 404);
        $target = InstructionStatus::tryFrom($status);
        abort_unless($target !== null, 404);
        $data = $request->validate([
            'ownership_check' => ['sometimes', 'array'],
            'terms' => ['sometimes', 'array'],
            'disclosures' => ['sometimes', 'array'],
        ]);

        return (new InstructionResource($transition->handle($instruction, $user->current_team_id, $user->getAuthIdentifier(), $target, $data)))->response();
    }
}
