<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertyManagementApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Liberu\RealEstate\PropertyManagement\Application\CreateMaintenanceRequest;
use Liberu\RealEstate\PropertyManagement\Application\UpdateMaintenanceRequest;
use Liberu\RealEstate\PropertyManagement\Models\MaintenanceRequest;
use Liberu\RealEstate\PropertyManagementApi\Http\Resources\MaintenanceRequestResource;

final class MaintenanceRequestController
{
    public function index(Request $request): JsonResponse
    {
        $team = $request->user()?->current_team_id;
        abort_unless($team !== null, 403);

        return MaintenanceRequestResource::collection(MaintenanceRequest::query()->forTeam($team)->latest()->paginate(min(100, max(1, $request->integer('page_size', 25)))))->response();
    }

    public function store(Request $request, CreateMaintenanceRequest $create): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->current_team_id !== null, 403);

        return (new MaintenanceRequestResource($create->handle($user->current_team_id, $user->getAuthIdentifier(), $request->validate($this->rules(false)))))->response()->setStatusCode(201);
    }

    public function show(Request $request, MaintenanceRequest $maintenance): JsonResponse
    {
        $this->assertTeam($request, $maintenance);

        return (new MaintenanceRequestResource($maintenance))->response();
    }

    public function update(Request $request, MaintenanceRequest $maintenance, UpdateMaintenanceRequest $update): JsonResponse
    {
        $this->assertTeam($request, $maintenance);

        return (new MaintenanceRequestResource($update->handle($maintenance, $request->user()->current_team_id, $request->validate($this->rules(true)))))->response();
    }

    private function assertTeam(Request $request, MaintenanceRequest $maintenance): void
    {
        abort_unless((string) $request->user()?->current_team_id === (string) $maintenance->team_id, 404);
    }

    private function rules(bool $sometimes): array
    {
        $presence = $sometimes ? ['sometimes'] : [];

        return [
            'title' => [...$presence, ...($sometimes ? ['string'] : ['required', 'string']), 'max:255'],
            'description' => [...$presence, ...($sometimes ? ['string'] : ['required', 'string'])],
            'property_id' => [...$presence, ...($sometimes ? ['integer'] : ['required', 'integer'])],
            'party_id' => [...$presence, 'nullable', 'integer'], 'vendor_id' => [...$presence, 'nullable', 'integer'],
            'status' => [...$presence, Rule::in(['pending', 'in_progress', 'completed', 'cancelled'])], 'priority' => [...$presence, Rule::in(['low', 'normal', 'high', 'urgent'])],
            'requested_date' => [...$presence, ...($sometimes ? ['date'] : ['required', 'date'])], 'photos' => [...$presence, 'nullable', 'array'], 'quote_references' => [...$presence, 'nullable', 'array'], 'invoice_reference' => [...$presence, 'nullable', 'string', 'max:255'], 'payment_status' => [...$presence, 'nullable', Rule::in(['not_applicable', 'pending', 'paid', 'overdue'])], 'completed_at' => [...$presence, 'nullable', 'date'],
        ];
    }
}
