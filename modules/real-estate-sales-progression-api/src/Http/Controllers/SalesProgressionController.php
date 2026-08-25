<?php

declare(strict_types=1);

namespace Liberu\RealEstate\SalesProgressionApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Liberu\RealEstate\SalesProgression\Application\CreateSalesProgression;
use Liberu\RealEstate\SalesProgression\Application\DeleteSalesProgression;
use Liberu\RealEstate\SalesProgression\Application\TransitionSalesProgression;
use Liberu\RealEstate\SalesProgression\Application\UpdateSalesProgression;
use Liberu\RealEstate\SalesProgression\Application\UpdateSalesProgressionSection;
use Liberu\RealEstate\SalesProgression\Domain\SalesProgressionSection;
use Liberu\RealEstate\SalesProgression\Domain\SalesProgressionStatus;
use Liberu\RealEstate\SalesProgression\Models\SalesProgression;
use Liberu\RealEstate\SalesProgressionApi\Http\Resources\SalesProgressionResource;

final class SalesProgressionController
{
    public function index(Request $request): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless($teamId !== null, 403);
        $size = max(1, min($request->integer('page_size', 25), 100));

        return SalesProgressionResource::collection(SalesProgression::query()->forTeam($teamId)->latest()->paginate($size))->response();
    }

    public function store(Request $request, CreateSalesProgression $create): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->current_team_id !== null, 403);
        $data = $request->validate(['subject' => ['required', 'string', 'max:255'], 'property_id' => ['nullable', 'integer'], 'offer_id' => ['nullable', 'integer'], 'status' => ['sometimes', 'string', 'in:in_progress,on_hold,exchanged,completed,fallen_through'], 'milestones' => ['sometimes', 'array'], 'chain' => ['sometimes', 'array'], 'professionals' => ['sometimes', 'array'], 'completion_controls' => ['sometimes', 'array'], 'exchanged_at' => ['nullable', 'date'], 'completed_at' => ['nullable', 'date'], 'notes' => ['nullable', 'string']]);

        return (new SalesProgressionResource($create->handle($user->current_team_id, $user->getAuthIdentifier(), $data)))->response()->setStatusCode(201);
    }

    public function show(Request $request, SalesProgression $salesProgression): JsonResponse
    {
        abort_unless((string) $request->user()?->current_team_id === (string) $salesProgression->team_id, 404);

        return (new SalesProgressionResource($salesProgression))->response();
    }

    public function update(Request $request, SalesProgression $salesProgression, UpdateSalesProgression $update): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless((string) $teamId === (string) $salesProgression->team_id, 404);
        $data = $request->validate(['subject' => ['sometimes', 'string', 'max:255'], 'milestones' => ['sometimes', 'array'], 'chain' => ['sometimes', 'array'], 'professionals' => ['sometimes', 'array'], 'completion_controls' => ['sometimes', 'array'], 'exchanged_at' => ['nullable', 'date'], 'completed_at' => ['nullable', 'date'], 'notes' => ['nullable', 'string']]);

        return (new SalesProgressionResource($update->handle($salesProgression, $teamId, $data)))->response();
    }

    public function transition(Request $request, SalesProgression $salesProgression, string $status, TransitionSalesProgression $transition): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless((string) $teamId === (string) $salesProgression->team_id, 404);

        return (new SalesProgressionResource($transition->handle($salesProgression, $teamId, SalesProgressionStatus::from($status))))->response();
    }

    public function updateSection(Request $request, SalesProgression $salesProgression, string $section, UpdateSalesProgressionSection $update): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless((string) $teamId === (string) $salesProgression->team_id, 404);
        $data = $request->validate(['value' => ['required', 'array']]);

        return (new SalesProgressionResource($update->handle($salesProgression, $teamId, SalesProgressionSection::from($section), $data['value'])))->response();
    }

    public function destroy(Request $request, SalesProgression $salesProgression, DeleteSalesProgression $delete): Response
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless((string) $teamId === (string) $salesProgression->team_id, 404);
        $delete->handle($salesProgression, $teamId);

        return response()->noContent();
    }
}
