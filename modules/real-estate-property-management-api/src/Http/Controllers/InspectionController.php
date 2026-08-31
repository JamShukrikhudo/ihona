<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertyManagementApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Liberu\RealEstate\PropertyManagement\Application\CreateInspection;
use Liberu\RealEstate\PropertyManagement\Application\UpdateInspection;
use Liberu\RealEstate\PropertyManagement\Models\Inspection;
use Liberu\RealEstate\PropertyManagementApi\Http\Resources\InspectionResource;

final class InspectionController
{
    public function index(Request $request): JsonResponse
    {
        $team = $request->user()?->current_team_id;
        abort_unless($team !== null, 403);

        return InspectionResource::collection(Inspection::query()->forTeam($team)->latest('scheduled_at')->paginate(min(100, max(1, $request->integer('page_size', 25)))))->response();
    }

    public function store(Request $request, CreateInspection $create): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->current_team_id !== null, 403);

        return (new InspectionResource($create->handle($user->current_team_id, $user->getAuthIdentifier(), $request->validate($this->rules(false)))))->response()->setStatusCode(201);
    }

    public function show(Request $request, Inspection $inspection): JsonResponse
    {
        $this->assertTeam($request, $inspection);

        return (new InspectionResource($inspection))->response();
    }

    public function update(Request $request, Inspection $inspection, UpdateInspection $update): JsonResponse
    {
        $this->assertTeam($request, $inspection);

        return (new InspectionResource($update->handle($inspection, $request->user()->current_team_id, $request->validate($this->rules(true)))))->response();
    }

    private function assertTeam(Request $request, Inspection $inspection): void
    {
        abort_unless((string) $request->user()?->current_team_id === (string) $inspection->team_id, 404);
    }

    private function rules(bool $sometimes): array
    {
        $presence = $sometimes ? ['sometimes'] : [];

        return [
            'property_id' => [...$presence, ...($sometimes ? ['integer'] : ['required', 'integer'])],
            'party_id' => [...$presence, 'nullable', 'integer'], 'branch_id' => [...$presence, 'nullable', 'integer'], 'assigned_to' => [...$presence, 'nullable', 'integer'],
            'type' => [...$presence, ...($sometimes ? [Rule::in(['routine', 'check_in', 'check_out', 'mid_tenancy'])] : ['required', Rule::in(['routine', 'check_in', 'check_out', 'mid_tenancy'])])],
            'status' => [...$presence, Rule::in(['scheduled', 'in_progress', 'completed', 'cancelled'])],
            'scheduled_at' => [...$presence, ...($sometimes ? ['date'] : ['required', 'date'])], 'started_at' => [...$presence, 'nullable', 'date'], 'completed_at' => [...$presence, 'nullable', 'date'],
            'notes' => [...$presence, 'nullable', 'string'], 'areas' => [...$presence, 'nullable', 'array'], 'photos' => [...$presence, 'nullable', 'array'], 'damage_reports' => [...$presence, 'nullable', 'array'], 'signatures' => [...$presence, 'nullable', 'array'],
        ];
    }
}
