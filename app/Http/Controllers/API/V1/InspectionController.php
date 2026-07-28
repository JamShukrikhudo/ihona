<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Inspection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InspectionController extends TenantCrudController
{
    protected string $model = Inspection::class;

    protected string $routeParameter = 'inspection';

    protected array $searchable = ['notes'];

    protected array $filterable = ['property_id', 'tenant_id', 'assigned_to', 'type', 'status'];

    protected function rules(Request $request, ?Model $record = null): array
    {
        $teamId = $this->teamId($request);

        return [
            'property_id' => [$record ? 'sometimes' : 'required', Rule::exists('properties', 'id')->where('team_id', $teamId)],
            'tenant_id' => ['nullable', Rule::exists('tenants', 'id')->where('team_id', $teamId)],
            'branch_id' => ['nullable', Rule::exists('branches', 'id')->where('team_id', $teamId)],
            'assigned_to' => ['nullable', Rule::exists('team_user', 'user_id')->where('team_id', $teamId)],
            'type' => [$record ? 'sometimes' : 'required', Rule::in(['routine', 'check_in', 'check_out', 'mid_tenancy'])],
            'status' => ['sometimes', Rule::in(['scheduled', 'in_progress', 'completed', 'cancelled'])],
            'scheduled_at' => [$record ? 'sometimes' : 'required', 'date'],
            'started_at' => ['nullable', 'date'],
            'completed_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'areas' => ['nullable', 'array'],
            'areas.*.name' => ['required_with:areas', 'string', 'max:100'],
            'areas.*.condition' => ['nullable', Rule::in(['excellent', 'good', 'fair', 'poor'])],
            'areas.*.notes' => ['nullable', 'string'],
            'photos' => ['nullable', 'array'],
            'photos.*' => ['string', 'max:2048'],
            'damage_reports' => ['nullable', 'array'],
            'damage_reports.*.area' => ['required_with:damage_reports', 'string', 'max:100'],
            'damage_reports.*.description' => ['required_with:damage_reports', 'string'],
            'damage_reports.*.severity' => ['nullable', Rule::in(['minor', 'moderate', 'major'])],
            'signatures' => ['nullable', 'array'],
            'signatures.*.role' => ['required_with:signatures', Rule::in(['inspector', 'tenant', 'landlord'])],
            'signatures.*.name' => ['required_with:signatures', 'string', 'max:255'],
            'signatures.*.signed_at' => ['required_with:signatures', 'date'],
            'signatures.*.signature' => ['required_with:signatures', 'string'],
        ];
    }

    protected function prepareForCreate(Request $request, array $attributes): array
    {
        $attributes['created_by'] = $request->user()->id;

        return $attributes;
    }
}
