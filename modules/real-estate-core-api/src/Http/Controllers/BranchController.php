<?php

declare(strict_types=1);

namespace Liberu\RealEstate\CoreApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Liberu\RealEstate\Core\Application\CreateBranch;
use Liberu\RealEstate\Core\Application\DeleteBranch;
use Liberu\RealEstate\Core\Application\UpdateBranch;
use Liberu\RealEstate\Core\Models\Branch;

final class BranchController
{
    public function index(Request $request): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless($teamId !== null, 403);

        return response()->json(['data' => Branch::query()->forTeam($teamId)->latest()->paginate(max(1, min($request->integer('page_size', 25), 100)))]);
    }

    public function store(Request $request, CreateBranch $create): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->current_team_id !== null, 403);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'regex:/^[A-Za-z0-9-]{2,20}$/'],
            'address' => ['nullable', 'array'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'metadata' => ['sometimes', 'array'],
        ]);

        return response()->json(['data' => $create->handle($user->current_team_id, $validated)], 201);
    }

    public function show(Request $request, Branch $branch): JsonResponse
    {
        abort_unless($request->user()?->current_team_id === $branch->team_id, 404);

        return response()->json(['data' => $branch]);
    }

    public function update(Request $request, Branch $branch, UpdateBranch $update): JsonResponse
    {
        abort_unless($request->user()?->current_team_id === $branch->team_id, 404);
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'code' => ['sometimes', 'string', 'regex:/^[A-Za-z0-9-]{2,20}$/'],
            'address' => ['nullable', 'array'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'metadata' => ['sometimes', 'array'],
        ]);

        return response()->json(['data' => $update->handle($branch->team_id, $branch->getKey(), $validated)]);
    }

    public function destroy(Request $request, Branch $branch, DeleteBranch $delete): Response
    {
        abort_unless($request->user()?->current_team_id === $branch->team_id, 404);
        $delete->handle($branch->team_id, $branch->getKey());

        return response()->noContent();
    }
}
