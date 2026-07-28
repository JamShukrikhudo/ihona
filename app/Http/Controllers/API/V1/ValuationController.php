<?php

namespace App\Http\Controllers\API\V1;

use App\Models\PropertyValuation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ValuationController extends TenantCrudController
{
    protected string $model = PropertyValuation::class;

    protected string $routeParameter = 'valuation';

    protected array $searchable = ['valuer_name', 'valuer_company', 'notes'];

    protected array $filterable = ['property_id', 'valuation_type', 'status', 'user_id', 'assigned_to', 'outcome'];

    protected function rules(Request $request, ?Model $record = null): array
    {
        $teamId = $this->teamId($request);

        return [
            'property_id' => [$record ? 'sometimes' : 'required', Rule::exists('properties', 'id')->where('team_id', $teamId)],
            'valuation_type' => [$record ? 'sometimes' : 'required', Rule::in(['market', 'rental', 'commercial', 'insurance', 'mortgage'])],
            'estimated_value' => ['nullable', 'numeric', 'min:0'],
            'market_value' => ['nullable', 'numeric', 'min:0'],
            'rental_value' => ['nullable', 'numeric', 'min:0'],
            'valuation_date' => [$record ? 'sometimes' : 'required', 'date'],
            'scheduled_at' => ['nullable', 'date'],
            'assigned_to' => ['nullable', Rule::exists('team_user', 'user_id')->where('team_id', $teamId)],
            'follow_up_at' => ['nullable', 'date', 'after_or_equal:scheduled_at'],
            'completed_at' => ['nullable', 'date'],
            'valuer_name' => ['nullable', 'string', 'max:255'],
            'valuer_company' => ['nullable', 'string', 'max:255'],
            'valuation_method' => ['nullable', 'string', 'max:255'],
            'comparable_properties' => ['nullable', 'array'],
            'comparable_properties.*' => ['array'],
            'comparable_properties.*.property_id' => [
                'nullable',
                Rule::exists('properties', 'id')->where('team_id', $teamId),
            ],
            'comparable_properties.*.address' => ['nullable', 'string', 'max:500'],
            'comparable_properties.*.value' => ['nullable', 'numeric', 'min:0'],
            'comparable_properties.*.currency' => ['nullable', 'string', 'size:3'],
            'comparable_properties.*.notes' => ['nullable', 'string', 'max:2000'],
            'market_conditions' => ['nullable', 'string'],
            'property_condition' => ['nullable', 'string'],
            'location_factors' => ['nullable', 'array'],
            'notes' => ['nullable', 'string'],
            'confidence_level' => ['sometimes', 'integer', 'between:0,100'],
            'valid_until' => ['nullable', 'date', 'after_or_equal:valuation_date'],
            'status' => ['sometimes', Rule::in([
                'scheduled', 'active', 'completed', 'cancelled', 'expired', 'superseded',
            ])],
            'outcome' => ['nullable', Rule::in([
                'instruction_won', 'instruction_lost', 'follow_up', 'no_decision',
            ])],
            'outcome_notes' => ['nullable', 'string', 'max:5000'],
        ];
    }

    protected function prepareForCreate(Request $request, array $attributes): array
    {
        $attributes['user_id'] = $request->user()->id;
        $attributes['assigned_to'] ??= $request->user()->id;
        $attributes['scheduled_at'] ??= Carbon::parse($attributes['valuation_date'])->startOfDay();

        return $attributes;
    }
}
