<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Buyer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BuyerController extends TenantCrudController
{
    protected string $model = Buyer::class;

    protected string $routeParameter = 'buyer';

    protected array $searchable = ['name', 'first_name', 'last_name', 'email', 'phone'];

    protected array $filterable = ['status'];

    protected function rules(Request $request, ?Model $record = null): array
    {
        return [
            'name' => [$record ? 'sometimes' : 'required', 'string', 'max:255'],
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => [
                $record ? 'sometimes' : 'required',
                'email',
                'max:255',
                Rule::unique('buyers', 'email')->ignore($record?->getKey()),
            ],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'user_id' => ['nullable', Rule::exists('team_user', 'user_id')->where('team_id', $this->teamId($request))],
            'status' => ['sometimes', Rule::in(['active', 'inactive', 'archived'])],
            'search_criteria' => ['nullable', 'array'],
            'search_criteria.min_price' => ['nullable', 'numeric', 'min:0'],
            'search_criteria.max_price' => ['nullable', 'numeric', 'min:0', Rule::when(
                $request->filled('search_criteria.min_price'),
                'gte:search_criteria.min_price',
            )],
            'search_criteria.min_bedrooms' => ['nullable', 'integer', 'min:0'],
            'search_criteria.max_bedrooms' => ['nullable', 'integer', 'min:0', Rule::when(
                $request->filled('search_criteria.min_bedrooms'),
                'gte:search_criteria.min_bedrooms',
            )],
            'search_criteria.min_bathrooms' => ['nullable', 'integer', 'min:0'],
            'search_criteria.min_area' => ['nullable', 'numeric', 'min:0'],
            'search_criteria.max_area' => ['nullable', 'numeric', 'min:0', Rule::when(
                $request->filled('search_criteria.min_area'),
                'gte:search_criteria.min_area',
            )],
            'search_criteria.property_type' => ['nullable', 'string', 'max:100'],
            'search_criteria.location' => ['nullable', 'string', 'max:255'],
            'search_criteria.postal_codes' => ['nullable', 'array'],
            'search_criteria.postal_codes.*' => ['string', 'max:30'],
            'search_criteria.required_features' => ['nullable', 'array'],
            'search_criteria.required_features.*' => ['string', 'max:100'],
            'search_criteria.availability' => ['nullable', 'array', 'max:10'],
            'search_criteria.availability.*' => ['string', 'distinct', Rule::in(['available', 'coming_soon', 'to_let'])],
            'search_criteria.latitude' => ['nullable', 'numeric', 'between:-90,90', 'required_with:search_criteria.longitude,search_criteria.radius_km'],
            'search_criteria.longitude' => ['nullable', 'numeric', 'between:-180,180', 'required_with:search_criteria.latitude,search_criteria.radius_km'],
            'search_criteria.radius_km' => ['nullable', 'numeric', 'gt:0', 'max:1000', 'required_with:search_criteria.latitude,search_criteria.longitude'],
            'search_criteria.required_schools' => ['nullable', 'array', 'max:50'],
            'search_criteria.required_schools.*' => ['string', 'distinct', 'max:255'],
            'search_criteria.min_transit_score' => ['nullable', 'numeric', 'between:0,100'],
        ];
    }
}
