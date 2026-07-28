<?php

namespace App\Http\Controllers\API\V1;

use App\Models\PropertyValuation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ValuationController extends TenantCrudController
{
    protected string $model = PropertyValuation::class;
    protected string $routeParameter = 'valuation';
    protected array $searchable = ['valuer_name', 'valuer_company', 'notes'];
    protected array $filterable = ['property_id', 'valuation_type', 'status', 'user_id'];

    protected function rules(Request $request, ?Model $record = null): array
    {
        return [
            'property_id' => [$record ? 'sometimes' : 'required', Rule::exists('properties', 'id')->where('team_id', $this->teamId($request))],
            'valuation_type' => [$record ? 'sometimes' : 'required', Rule::in(['market', 'rental', 'commercial', 'insurance', 'mortgage'])],
            'estimated_value' => ['nullable', 'numeric', 'min:0'],
            'market_value' => ['nullable', 'numeric', 'min:0'],
            'rental_value' => ['nullable', 'numeric', 'min:0'],
            'valuation_date' => [$record ? 'sometimes' : 'required', 'date'],
            'valuer_name' => ['nullable', 'string', 'max:255'],
            'valuer_company' => ['nullable', 'string', 'max:255'],
            'valuation_method' => ['nullable', 'string', 'max:255'],
            'comparable_properties' => ['nullable', 'array'],
            'market_conditions' => ['nullable', 'string'],
            'property_condition' => ['nullable', 'string'],
            'location_factors' => ['nullable', 'array'],
            'notes' => ['nullable', 'string'],
            'confidence_level' => ['sometimes', 'integer', 'between:0,100'],
            'valid_until' => ['nullable', 'date', 'after_or_equal:valuation_date'],
            'status' => ['sometimes', Rule::in(['active', 'expired', 'superseded'])],
        ];
    }

    protected function prepareForCreate(Request $request, array $attributes): array
    {
        $attributes['user_id'] = $request->user()->id;
        return $attributes;
    }
}
