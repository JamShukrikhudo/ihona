<?php

namespace App\Http\Controllers\API\V1;

use App\Models\LeaseAgreement;
use App\Models\User;
use App\Services\WorkflowNotifier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TenancyAgreementController extends TenantCrudController
{
    protected string $model = LeaseAgreement::class;

    protected string $routeParameter = 'tenancy_agreement';

    protected array $searchable = ['terms', 'terms_and_conditions'];

    protected array $filterable = ['property_id', 'tenant_id', 'landlord_id', 'status'];

    public function __construct(private readonly WorkflowNotifier $notifications) {}

    protected function rules(Request $request, ?Model $record = null): array
    {
        $teamId = $this->teamId($request);

        return [
            'tenant_id' => [$record ? 'sometimes' : 'required', Rule::exists('tenants', 'id')->where('team_id', $teamId)],
            'property_id' => [$record ? 'sometimes' : 'required', Rule::exists('properties', 'id')->where('team_id', $teamId)],
            'landlord_id' => ['nullable', Rule::exists('team_user', 'user_id')->where('team_id', $teamId)],
            'start_date' => [$record ? 'sometimes' : 'required', 'date'],
            'end_date' => [$record ? 'sometimes' : 'required', 'date', 'after:start_date'],
            'monthly_rent' => [$record ? 'sometimes' : 'required', 'numeric', 'min:0'],
            'security_deposit' => ['nullable', 'numeric', 'min:0'],
            'deposit_scheme' => ['nullable', 'string', 'max:255'],
            'deposit_reference' => ['nullable', 'string', 'max:255'],
            'payment_frequency' => ['nullable', Rule::in(['weekly', 'fortnightly', 'monthly', 'quarterly', 'annually'])],
            'status' => ['sometimes', Rule::in(['draft', 'pending_signature', 'active', 'notice_served', 'ended', 'terminated', 'renewed'])],
            'terms' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'terms_and_conditions' => ['nullable', 'string'],
            'is_signed' => ['sometimes', 'boolean'],
            'landlord_signed' => ['sometimes', 'boolean'],
            'tenant_signed' => ['sometimes', 'boolean'],
            'renewal_of_id' => ['nullable', Rule::exists('lease_agreements', 'id')->where('team_id', $teamId)],
            'notice_type' => ['nullable', Rule::in(['landlord', 'tenant', 'mutual'])],
            'notice_served_at' => ['nullable', 'date'],
            'notice_expires_at' => ['nullable', 'date', 'after_or_equal:notice_served_at'],
            'ended_at' => ['nullable', 'date'],
            'end_reason' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function renew(Request $request, int $tenancyAgreement): JsonResponse
    {
        $original = $this->teamQuery($request)->findOrFail($tenancyAgreement);
        $attributes = $request->validate([
            'start_date' => ['required', 'date', 'after_or_equal:'.$original->end_date->toDateString()],
            'end_date' => ['required', 'date', 'after:start_date'],
            'monthly_rent' => ['required', 'numeric', 'min:0'],
            'security_deposit' => ['nullable', 'numeric', 'min:0'],
        ]);

        $renewal = $original->replicate([
            'status', 'is_signed', 'landlord_signed', 'tenant_signed',
            'notice_type', 'notice_served_at', 'notice_expires_at', 'ended_at', 'end_reason',
        ]);
        $renewal->fill($attributes + [
            'team_id' => $this->teamId($request),
            'renewal_of_id' => $original->id,
            'status' => 'draft',
            'is_signed' => false,
            'landlord_signed' => false,
            'tenant_signed' => false,
        ])->save();
        $original->update(['status' => 'renewed']);
        $recipient = User::find($original->landlord_id) ?? $request->user();
        $this->notifications->notify(
            $this->teamId($request),
            $recipient,
            'tenancy.renewed',
            'Tenancy renewed',
            "The tenancy now ends {$renewal->end_date->toDateString()}.",
            [
                'tenancy_agreement_id' => $renewal->id,
                'renewal_of_id' => $original->id,
                'property_id' => $renewal->property_id,
            ],
        );

        return response()->json(['data' => $renewal->fresh()], 201);
    }

    public function notice(Request $request, int $tenancyAgreement): JsonResponse
    {
        $record = $this->teamQuery($request)->findOrFail($tenancyAgreement);
        $attributes = $request->validate([
            'notice_type' => ['required', Rule::in(['landlord', 'tenant', 'mutual'])],
            'notice_served_at' => ['required', 'date'],
            'notice_expires_at' => ['required', 'date', 'after_or_equal:notice_served_at'],
            'end_reason' => ['nullable', 'string', 'max:1000'],
        ]);
        $record->update($attributes + ['status' => 'notice_served']);

        return response()->json(['data' => $record->fresh()]);
    }
}
