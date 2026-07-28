<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContractorController extends TenantCrudController
{
    protected string $model = Vendor::class;

    protected string $routeParameter = 'contractor';

    protected array $searchable = ['company_name', 'contact_person', 'email', 'phone', 'vendor_type'];

    protected array $filterable = ['vendor_type', 'status', 'preferred_vendor'];

    protected function rules(Request $request, ?Model $record = null): array
    {
        return [
            'company_name' => [$record ? 'sometimes' : 'required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'mobile' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:30'],
            'country' => ['sometimes', 'string', 'size:2'],
            'website' => ['nullable', 'url', 'max:2048'],
            'vendor_type' => [$record ? 'sometimes' : 'required', 'string', 'max:100'],
            'specializations' => ['nullable', 'array'],
            'specializations.*' => ['string', 'max:100'],
            'rating' => ['sometimes', 'numeric', 'between:0,5'],
            'status' => ['sometimes', Rule::in(['active', 'inactive', 'blacklisted'])],
            'preferred_vendor' => ['sometimes', 'boolean'],
            'insurance_valid_until' => ['nullable', 'date'],
            'certifications' => ['nullable', 'array'],
            'payment_terms' => ['nullable', 'string', 'max:255'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'daily_rate' => ['nullable', 'numeric', 'min:0'],
            'emergency_contact' => ['nullable', 'string', 'max:255'],
            'emergency_phone' => ['nullable', 'string', 'max:50'],
            'availability_hours' => ['nullable', 'array'],
            'service_areas' => ['nullable', 'array'],
            'notes' => ['nullable', 'string'],
            'tax_number' => ['nullable', 'string', 'max:100'],
        ];
    }

    protected function prepareForCreate(Request $request, array $attributes): array
    {
        $attributes['added_by'] = $request->user()->id;

        return $attributes;
    }
}
