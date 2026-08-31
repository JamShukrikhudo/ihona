<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertyManagementApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Liberu\RealEstate\PropertyManagement\Application\CreateWorkOrder;
use Liberu\RealEstate\PropertyManagement\Application\RecordWorkOrderUpdate;
use Liberu\RealEstate\PropertyManagement\Models\WorkOrder;
use Liberu\RealEstate\PropertyManagementApi\Http\Resources\WorkOrderResource;
use Liberu\RealEstate\PropertyManagementApi\Http\Resources\WorkOrderUpdateResource;

final class WorkOrderController
{
    public function index(Request $request): JsonResponse
    {
        $team = $request->user()?->current_team_id;
        abort_unless($team !== null, 403);

        return WorkOrderResource::collection(WorkOrder::query()->forTeam($team)->latest()->paginate(min(100, max(1, $request->integer('page_size', 25)))))->response();
    }

    public function store(Request $request, CreateWorkOrder $create): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->current_team_id !== null, 403);

        return (new WorkOrderResource($create->handle($user->current_team_id, $user->getAuthIdentifier(), $request->validate($this->rules(false)))))->response()->setStatusCode(201);
    }

    public function show(Request $request, WorkOrder $workOrder): JsonResponse
    {
        $this->assertTeam($request, $workOrder);

        return (new WorkOrderResource($workOrder))->response();
    }

    public function update(Request $request, WorkOrder $workOrder): JsonResponse
    {
        $this->assertTeam($request, $workOrder);
        $workOrder->forceFill($request->validate($this->rules(true)))->save();

        return (new WorkOrderResource($workOrder->refresh()))->response();
    }

    public function updates(Request $request, WorkOrder $workOrder): JsonResponse
    {
        $this->assertTeam($request, $workOrder);

        return WorkOrderUpdateResource::collection($workOrder->updates()->latest('update_date')->paginate(min(100, max(1, $request->integer('page_size', 25)))))->response();
    }

    public function storeUpdate(Request $request, WorkOrder $workOrder, RecordWorkOrderUpdate $record): JsonResponse
    {
        $this->assertTeam($request, $workOrder);
        $data = $request->validate(['update_type' => ['required', Rule::in(['status_change', 'progress', 'issue', 'completion', 'note'])], 'status_change' => ['nullable', Rule::in(['pending', 'approved', 'scheduled', 'in_progress', 'completed', 'cancelled'])], 'description' => ['required', 'string'], 'progress_percentage' => ['nullable', 'integer', 'between:0,100'], 'time_spent' => ['nullable', 'numeric', 'min:0'], 'materials_used' => ['nullable', 'array'], 'issues_encountered' => ['nullable', 'array'], 'next_steps' => ['nullable', 'string'], 'is_customer_visible' => ['sometimes', 'boolean']]);

        return (new WorkOrderUpdateResource($record->handle($workOrder, $request->user()->current_team_id, $request->user()->getAuthIdentifier(), $data)))->response()->setStatusCode(201);
    }

    private function assertTeam(Request $request, WorkOrder $order): void
    {
        abort_unless((string) $request->user()?->current_team_id === (string) $order->team_id, 404);
    }

    private function rules(bool $sometimes): array
    {
        $presence = $sometimes ? ['sometimes'] : [];

        return ['property_id' => [...$presence, ...($sometimes ? ['integer'] : ['required', 'integer'])], 'maintenance_request_id' => [...$presence, 'nullable', 'integer'], 'vendor_id' => [...$presence, 'nullable', 'integer'], 'title' => [...$presence, ...($sometimes ? ['string'] : ['required', 'string']), 'max:255'], 'description' => [...$presence, ...($sometimes ? ['string'] : ['required', 'string'])], 'work_type' => [...$presence, ...($sometimes ? ['string', 'max:100'] : ['required', 'string', 'max:100'])], 'priority' => [...$presence, 'integer', 'between:1,4'], 'status' => [...$presence, Rule::in(['pending', 'approved', 'scheduled', 'in_progress', 'completed', 'cancelled'])], 'scheduled_date' => [...$presence, 'nullable', 'date'], 'started_date' => [...$presence, 'nullable', 'date'], 'completed_date' => [...$presence, 'nullable', 'date'], 'estimated_cost' => [...$presence, 'nullable', 'numeric', 'min:0'], 'actual_cost' => [...$presence, 'nullable', 'numeric', 'min:0'], 'estimated_hours' => [...$presence, 'nullable', 'numeric', 'min:0'], 'actual_hours' => [...$presence, 'nullable', 'numeric', 'min:0'], 'materials_cost' => [...$presence, 'nullable', 'numeric', 'min:0'], 'labor_cost' => [...$presence, 'nullable', 'numeric', 'min:0'], 'emergency_job' => [...$presence, 'boolean'], 'requires_access' => [...$presence, 'boolean'], 'access_instructions' => [...$presence, 'nullable', 'string'], 'safety_requirements' => [...$presence, 'nullable', 'array'], 'completion_notes' => [...$presence, 'nullable', 'string'], 'customer_satisfaction' => [...$presence, 'nullable', 'integer', 'between:1,5'], 'invoice_number' => [...$presence, 'nullable', 'string', 'max:100'], 'payment_status' => [...$presence, Rule::in(['not_applicable', 'pending', 'part_paid', 'paid', 'overdue'])]];
    }
}
