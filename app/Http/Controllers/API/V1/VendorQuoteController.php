<?php

namespace App\Http\Controllers\API\V1;

use App\Models\VendorQuote;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VendorQuoteController extends TenantCrudController
{
    protected string $model = VendorQuote::class;

    protected string $routeParameter = 'contractor_quote';

    protected array $searchable = ['work_description', 'notes'];

    protected array $filterable = ['vendor_id', 'property_id', 'maintenance_request_id', 'status'];

    protected function rules(Request $request, ?Model $record = null): array
    {
        $teamId = $this->teamId($request);

        return [
            'vendor_id' => [$record ? 'sometimes' : 'required', Rule::exists('vendors', 'id')->where('team_id', $teamId)],
            'property_id' => [$record ? 'sometimes' : 'required', Rule::exists('properties', 'id')->where('team_id', $teamId)],
            'maintenance_request_id' => ['nullable', Rule::exists('maintenance_requests', 'id')->where('team_id', $teamId)],
            'work_description' => [$record ? 'sometimes' : 'required', 'string'],
            'quote_amount' => [$record ? 'sometimes' : 'required', 'numeric', 'min:0'],
            'labor_cost' => ['nullable', 'numeric', 'min:0'],
            'materials_cost' => ['nullable', 'numeric', 'min:0'],
            'additional_costs' => ['nullable', 'numeric', 'min:0'],
            'quote_date' => [$record ? 'sometimes' : 'required', 'date'],
            'valid_until' => [$record ? 'sometimes' : 'required', 'date', 'after_or_equal:quote_date'],
            'estimated_duration' => ['nullable', 'integer', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'completion_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'terms_conditions' => ['nullable', 'string'],
            'status' => ['sometimes', Rule::in(['pending', 'accepted', 'rejected', 'expired', 'withdrawn'])],
            'notes' => ['nullable', 'string'],
            'rejection_reason' => ['nullable', 'string', 'required_if:status,rejected'],
        ];
    }

    protected function prepareForCreate(Request $request, array $attributes): array
    {
        $attributes['requested_by'] = $request->user()->id;

        return $attributes;
    }

    public function decide(Request $request, int $contractorQuote): JsonResponse
    {
        $quote = $this->teamQuery($request)->findOrFail($contractorQuote);
        abort_unless($quote->status === 'pending', 422, 'Only pending quotes can be decided.');
        $attributes = $request->validate([
            'decision' => ['required', Rule::in(['accepted', 'rejected'])],
            'rejection_reason' => ['nullable', 'string', 'required_if:decision,rejected'],
        ]);
        $quote->update([
            'status' => $attributes['decision'],
            'approved_by' => $attributes['decision'] === 'accepted' ? $request->user()->id : null,
            'rejection_reason' => $attributes['rejection_reason'] ?? null,
        ]);

        return response()->json(['data' => $quote->fresh()]);
    }
}
