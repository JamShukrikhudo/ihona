<?php

namespace App\Http\Controllers\API\V1;

use App\Models\MaintenanceRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MaintenanceController extends TenantCrudController
{
    protected string $model = MaintenanceRequest::class;
    protected string $routeParameter = 'maintenance';
    protected array $searchable = ['title', 'description'];
    protected array $filterable = ['property_id', 'tenant_id', 'contractor_id', 'status', 'priority'];

    protected function rules(Request $request, ?Model $record = null): array
    {
        $teamId = $this->teamId($request);
        return [
            'title' => [$record ? 'sometimes' : 'required', 'string', 'max:255'],
            'description' => [$record ? 'sometimes' : 'required', 'string'],
            'status' => ['sometimes', Rule::in(['pending', 'in_progress', 'completed', 'cancelled'])],
            'priority' => ['sometimes', Rule::in(['low', 'normal', 'high', 'urgent'])],
            'requested_date' => [$record ? 'sometimes' : 'required', 'date'],
            'tenant_id' => [$record ? 'sometimes' : 'required', Rule::exists('tenants', 'id')->where('team_id', $teamId)],
            'property_id' => [$record ? 'sometimes' : 'required', Rule::exists('properties', 'id')->where('team_id', $teamId)],
            'contractor_id' => ['nullable', Rule::exists('contractors', 'id')->where('team_id', $teamId)],
            'photos' => ['nullable', 'array'],
            'photos.*' => ['string', 'max:2048'],
            'quote_references' => ['nullable', 'array'],
            'invoice_reference' => ['nullable', 'string', 'max:255'],
            'payment_status' => ['nullable', Rule::in(['not_applicable', 'pending', 'paid', 'overdue'])],
            'completed_at' => ['nullable', 'date'],
        ];
    }
}
