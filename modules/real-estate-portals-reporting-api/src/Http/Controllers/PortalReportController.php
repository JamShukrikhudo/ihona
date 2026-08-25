<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PortalsReportingApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Liberu\RealEstate\PortalsReporting\Application\CreatePortalReport;
use Liberu\RealEstate\PortalsReporting\Application\DeletePortalReport;
use Liberu\RealEstate\PortalsReporting\Application\UpdatePortalReport;
use Liberu\RealEstate\PortalsReporting\Models\PortalReport;
use Liberu\RealEstate\PortalsReportingApi\Http\Resources\PortalReportResource;

final class PortalReportController
{
    public function index(Request $request): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless($teamId !== null, 403);
        $size = max(1, min($request->integer('page_size', 25), 100));

        return PortalReportResource::collection(PortalReport::query()->forTeam($teamId)->latest()->paginate($size))->response();
    }

    public function store(Request $request, CreatePortalReport $create): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->current_team_id !== null, 403);
        $data = $request->validate(['portal' => ['required', 'string', 'max:120'], 'report_type' => ['required', 'string', 'max:120'], 'property_id' => ['nullable', 'integer'], 'listing_id' => ['nullable', 'integer'], 'status' => ['sometimes', 'string', 'in:draft,queued,published,failed,expired,archived'], 'payload' => ['sometimes', 'array'], 'metrics' => ['sometimes', 'array'], 'published_at' => ['nullable', 'date'], 'generated_at' => ['nullable', 'date'], 'expires_at' => ['nullable', 'date'], 'error' => ['nullable', 'string']]);

        return (new PortalReportResource($create->handle($user->current_team_id, $user->getAuthIdentifier(), $data)))->response()->setStatusCode(201);
    }

    public function show(Request $request, PortalReport $portalReport): JsonResponse
    {
        abort_unless((string) $request->user()?->current_team_id === (string) $portalReport->team_id, 404);

        return (new PortalReportResource($portalReport))->response();
    }

    public function update(Request $request, PortalReport $portalReport, UpdatePortalReport $update): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless((string) $teamId === (string) $portalReport->team_id, 404);
        $data = $request->validate(['portal' => ['sometimes', 'string', 'max:120'], 'report_type' => ['sometimes', 'string', 'max:120'], 'status' => ['sometimes', 'string', 'in:draft,queued,published,failed,expired,archived'], 'payload' => ['sometimes', 'array'], 'metrics' => ['sometimes', 'array'], 'published_at' => ['nullable', 'date'], 'generated_at' => ['nullable', 'date'], 'expires_at' => ['nullable', 'date'], 'error' => ['nullable', 'string']]);

        return (new PortalReportResource($update->handle($portalReport, $teamId, $data)))->response();
    }

    public function destroy(Request $request, PortalReport $portalReport, DeletePortalReport $delete): Response
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless((string) $teamId === (string) $portalReport->team_id, 404);
        $delete->handle($portalReport, $teamId);

        return response()->noContent();
    }
}
