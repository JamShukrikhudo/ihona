<?php

namespace App\Http\Controllers\API\V1;

use App\Models\ComplianceItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ComplianceItemController extends TenantCrudController
{
    protected string $model = ComplianceItem::class;

    protected string $routeParameter = 'compliance_item';

    protected array $searchable = ['title', 'description', 'compliance_type', 'certificate_number'];

    protected array $filterable = [
        'property_id', 'compliance_type', 'status', 'priority_level', 'risk_level',
        'assigned_to', 'renewal_required',
    ];

    protected function rules(Request $request, ?Model $record = null): array
    {
        $teamId = $this->teamId($request);

        return [
            'property_id' => [$record ? 'sometimes' : 'required', Rule::exists('properties', 'id')->where('team_id', $teamId)],
            'compliance_type' => [
                $record ? 'sometimes' : 'required',
                Rule::in([
                    'epc', 'gas_safety', 'electrical', 'fire_safety', 'legionella',
                    'right_to_rent', 'identity', 'aml', 'deposit_protection',
                    'insurance', 'planning', 'licence', 'other',
                ]),
            ],
            'title' => [$record ? 'sometimes' : 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'regulation_reference' => ['nullable', 'string', 'max:255'],
            'required_by_date' => [$record ? 'sometimes' : 'required', 'date'],
            'completed_date' => ['nullable', 'date'],
            'status' => ['sometimes', Rule::in(['pending', 'in_progress', 'completed', 'overdue', 'not_applicable'])],
            'priority_level' => ['sometimes', 'integer', 'between:1,4'],
            'responsible_party' => ['nullable', 'string', 'max:255'],
            'cost_estimate' => ['nullable', 'numeric', 'min:0'],
            'actual_cost' => ['nullable', 'numeric', 'min:0'],
            'certificate_number' => ['nullable', 'string', 'max:255'],
            'certificate_expiry' => ['nullable', 'date'],
            'renewal_required' => ['sometimes', 'boolean'],
            'assigned_to' => ['nullable', Rule::exists('team_user', 'user_id')->where('team_id', $teamId)],
            'notes' => ['nullable', 'string'],
            'risk_level' => ['sometimes', 'integer', 'between:1,4'],
        ];
    }

    protected function prepareForCreate(Request $request, array $attributes): array
    {
        return $this->normaliseCompletion($attributes);
    }

    protected function prepareForUpdate(Request $request, Model $record, array $attributes): array
    {
        return $this->normaliseCompletion($attributes, $record);
    }

    private function normaliseCompletion(array $attributes, ?Model $record = null): array
    {
        $status = $attributes['status'] ?? $record?->status ?? 'pending';

        if ($status === 'completed' && empty($attributes['completed_date']) && ! $record?->completed_date) {
            $attributes['completed_date'] = now()->toDateString();
        }

        if (isset($attributes['status']) && $status !== 'completed') {
            $attributes['completed_date'] = $attributes['completed_date'] ?? null;
        }

        return $attributes;
    }
}
