<?php

namespace App\Http\Controllers\API\V1;

use App\Models\WorkOrder;
use App\Models\WorkOrderUpdate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WorkOrderController extends TenantCrudController
{
    protected string $model = WorkOrder::class;

    protected string $routeParameter = 'work_order';

    protected array $searchable = ['title', 'description', 'work_type', 'invoice_number'];

    protected array $filterable = [
        'property_id', 'maintenance_request_id', 'vendor_id', 'status',
        'priority', 'assigned_to', 'emergency_job', 'payment_status',
    ];

    protected function rules(Request $request, ?Model $record = null): array
    {
        $teamId = $this->teamId($request);

        return [
            'property_id' => [$record ? 'sometimes' : 'required', Rule::exists('properties', 'id')->where('team_id', $teamId)],
            'maintenance_request_id' => ['nullable', Rule::exists('maintenance_requests', 'id')->where('team_id', $teamId)],
            'vendor_id' => ['nullable', Rule::exists('vendors', 'id')->where('team_id', $teamId)],
            'title' => [$record ? 'sometimes' : 'required', 'string', 'max:255'],
            'description' => [$record ? 'sometimes' : 'required', 'string'],
            'work_type' => [$record ? 'sometimes' : 'required', 'string', 'max:100'],
            'priority' => ['sometimes', 'integer', 'between:1,4'],
            'status' => ['sometimes', Rule::in(['pending', 'approved', 'scheduled', 'in_progress', 'completed', 'cancelled'])],
            'scheduled_date' => ['nullable', 'date'],
            'started_date' => ['nullable', 'date'],
            'completed_date' => ['nullable', 'date'],
            'estimated_cost' => ['nullable', 'numeric', 'min:0'],
            'actual_cost' => ['nullable', 'numeric', 'min:0'],
            'estimated_hours' => ['nullable', 'numeric', 'min:0'],
            'actual_hours' => ['nullable', 'numeric', 'min:0'],
            'materials_cost' => ['nullable', 'numeric', 'min:0'],
            'labor_cost' => ['nullable', 'numeric', 'min:0'],
            'emergency_job' => ['sometimes', 'boolean'],
            'requires_access' => ['sometimes', 'boolean'],
            'access_instructions' => ['nullable', 'string'],
            'safety_requirements' => ['nullable', 'array'],
            'completion_notes' => ['nullable', 'string'],
            'customer_satisfaction' => ['nullable', 'integer', 'between:1,5'],
            'assigned_to' => ['nullable', Rule::exists('team_user', 'user_id')->where('team_id', $teamId)],
            'invoice_number' => ['nullable', 'string', 'max:100'],
            'payment_status' => ['sometimes', Rule::in(['not_applicable', 'pending', 'part_paid', 'paid', 'overdue'])],
        ];
    }

    protected function prepareForCreate(Request $request, array $attributes): array
    {
        $attributes['created_by'] = $request->user()->id;

        return $this->normaliseDates($attributes);
    }

    protected function prepareForUpdate(Request $request, Model $record, array $attributes): array
    {
        return $this->normaliseDates($attributes, $record);
    }

    public function updates(Request $request, int $workOrder): JsonResponse
    {
        $order = $this->teamQuery($request)->findOrFail($workOrder);

        return response()->json($order->workOrderUpdates()
            ->with('updatedBy:id,name')
            ->latest('update_date')
            ->paginate(min(max($request->integer('per_page', 20), 1), 100)));
    }

    public function storeUpdate(Request $request, int $workOrder): JsonResponse
    {
        $order = $this->teamQuery($request)->findOrFail($workOrder);
        $attributes = $request->validate([
            'update_type' => ['required', Rule::in(['status_change', 'progress', 'issue', 'completion', 'note'])],
            'status_change' => ['nullable', Rule::in(['approved', 'scheduled', 'in_progress', 'completed', 'cancelled'])],
            'description' => ['required', 'string'],
            'progress_percentage' => ['sometimes', 'integer', 'between:0,100'],
            'time_spent' => ['nullable', 'numeric', 'min:0'],
            'materials_used' => ['nullable', 'array'],
            'issues_encountered' => ['nullable', 'array'],
            'next_steps' => ['nullable', 'string'],
            'is_customer_visible' => ['sometimes', 'boolean'],
        ]);
        $update = WorkOrderUpdate::create([
            ...$attributes,
            'work_order_id' => $order->id,
            'updated_by' => $request->user()->id,
            'update_date' => now(),
        ]);

        if (! empty($attributes['status_change'])) {
            $order->update($this->normaliseDates(['status' => $attributes['status_change']], $order));
        }

        return response()->json(['data' => $update->fresh('updatedBy:id,name')], 201);
    }

    private function normaliseDates(array $attributes, ?Model $record = null): array
    {
        $status = $attributes['status'] ?? $record?->status ?? 'pending';

        if ($status === 'in_progress' && empty($attributes['started_date']) && ! $record?->started_date) {
            $attributes['started_date'] = now();
        }

        if ($status === 'completed' && empty($attributes['completed_date']) && ! $record?->completed_date) {
            $attributes['completed_date'] = now();
        }

        return $attributes;
    }
}
