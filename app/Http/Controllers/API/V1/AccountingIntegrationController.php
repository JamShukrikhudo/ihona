<?php

namespace App\Http\Controllers\API\V1;

use App\Models\AccountingIntegration;
use App\Models\AccountingLink;
use App\Models\AccountingSyncRun;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Contractor;
use App\Models\MaintenanceRequest;
use App\Models\Property;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AccountingIntegrationController extends TenantCrudController
{
    private const LINKABLES = [
        'contact' => Contact::class,
        'company' => Company::class,
        'contractor' => Contractor::class,
        'property' => Property::class,
        'maintenance' => MaintenanceRequest::class,
    ];

    protected string $model = AccountingIntegration::class;
    protected string $routeParameter = 'accounting_integration';
    protected array $searchable = ['name', 'provider'];
    protected array $filterable = ['provider', 'active', 'last_sync_status'];

    protected function rules(Request $request, ?Model $record = null): array
    {
        return [
            'provider' => [$record ? 'sometimes' : 'required', Rule::in(['liberu_accounting', 'quickbooks', 'xero', 'sage'])],
            'name' => [$record ? 'sometimes' : 'required', 'string', 'max:255'],
            'credentials' => ['nullable', 'array'],
            'settings' => ['nullable', 'array'],
            'active' => ['sometimes', 'boolean'],
        ];
    }

    public function links(Request $request): JsonResponse
    {
        $query = AccountingLink::where('team_id', $this->teamId($request))
            ->with('linkable')
            ->when($request->filled('integration_id'), fn ($query) => $query->where('accounting_integration_id', $request->integer('integration_id')))
            ->when($request->filled('link_type'), fn ($query) => $query->where('link_type', $request->string('link_type')))
            ->when($request->filled('payment_status'), fn ($query) => $query->where('payment_status', $request->string('payment_status')));
        return response()->json($query->latest('id')->paginate(
            min(max($request->integer('per_page', 20), 1), 100)
        ));
    }

    public function storeLink(Request $request): JsonResponse
    {
        $attributes = $this->validateLink($request);
        $attributes['team_id'] = $this->teamId($request);
        $attributes['linkable_type'] = self::LINKABLES[$attributes['linkable_type']];
        $this->assertLinkableTeam($attributes['linkable_type'], $attributes['linkable_id'], $attributes['team_id']);
        $link = AccountingLink::create($attributes);
        return response()->json(['data' => $link->fresh('linkable')], 201);
    }

    public function updateLink(Request $request, int $accountingLink): JsonResponse
    {
        $link = AccountingLink::where('team_id', $this->teamId($request))->findOrFail($accountingLink);
        $link->update($request->validate([
            'external_id' => ['nullable', 'string', 'max:255'],
            'invoice_reference' => ['nullable', 'string', 'max:255'],
            'payment_status' => ['sometimes', Rule::in(['unknown', 'draft', 'issued', 'pending', 'part_paid', 'paid', 'overdue', 'void'])],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'due_date' => ['nullable', 'date'],
            'metadata' => ['nullable', 'array'],
        ]));
        return response()->json(['data' => $link->fresh('linkable')]);
    }

    public function destroyLink(Request $request, int $accountingLink): JsonResponse
    {
        AccountingLink::where('team_id', $this->teamId($request))->findOrFail($accountingLink)->delete();
        return response()->json(null, 204);
    }

    public function sync(Request $request, int $accountingIntegration): JsonResponse
    {
        $integration = $this->teamQuery($request)->findOrFail($accountingIntegration);
        abort_unless($integration->active, 422, 'Inactive accounting integrations cannot be synchronized.');
        $run = AccountingSyncRun::create([
            'team_id' => $integration->team_id,
            'accounting_integration_id' => $integration->id,
            'requested_by' => $request->user()->id,
            'status' => 'running',
            'started_at' => now(),
        ]);
        $links = $integration->links()->get();
        $links->each->update(['last_synced_at' => now()]);
        $run->update([
            'status' => 'completed',
            'processed' => $links->count(),
            'succeeded' => $links->count(),
            'completed_at' => now(),
        ]);
        $integration->update([
            'last_synced_at' => now(),
            'last_sync_status' => 'completed',
            'last_error' => null,
        ]);
        return response()->json(['data' => $run->fresh()]);
    }

    public function runs(Request $request): JsonResponse
    {
        $query = AccountingSyncRun::where('team_id', $this->teamId($request))
            ->when($request->filled('integration_id'), fn ($query) => $query->where('accounting_integration_id', $request->integer('integration_id')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')));
        return response()->json($query->latest('started_at')->paginate(
            min(max($request->integer('per_page', 20), 1), 100)
        ));
    }

    public function summary(Request $request): JsonResponse
    {
        $links = AccountingLink::where('team_id', $this->teamId($request));
        $byStatus = (clone $links)->selectRaw('payment_status, count(*) as records, coalesce(sum(amount), 0) as amount')
            ->groupBy('payment_status')->get()->mapWithKeys(fn ($row) => [
                $row->payment_status => ['records' => (int) $row->records, 'amount' => (float) $row->amount],
            ]);
        return response()->json(['data' => [
            'currency_totals' => (clone $links)->whereNotNull('amount')->selectRaw('currency, sum(amount) as amount')
                ->groupBy('currency')->pluck('amount', 'currency')->map(fn ($amount) => (float) $amount),
            'payment_statuses' => $byStatus,
            'overdue_count' => (clone $links)->where('payment_status', 'overdue')->count(),
            'outstanding_amount' => (float) (clone $links)->whereIn('payment_status', ['issued', 'pending', 'part_paid', 'overdue'])->sum('amount'),
        ]]);
    }

    private function validateLink(Request $request): array
    {
        return $request->validate([
            'accounting_integration_id' => ['required', Rule::exists('accounting_integrations', 'id')->where('team_id', $this->teamId($request))],
            'link_type' => ['required', Rule::in(['customer', 'supplier', 'invoice', 'payment'])],
            'linkable_type' => ['required', Rule::in(array_keys(self::LINKABLES))],
            'linkable_id' => ['required', 'integer'],
            'external_id' => ['nullable', 'string', 'max:255'],
            'invoice_reference' => ['nullable', 'string', 'max:255'],
            'payment_status' => ['sometimes', Rule::in(['unknown', 'draft', 'issued', 'pending', 'part_paid', 'paid', 'overdue', 'void'])],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'due_date' => ['nullable', 'date'],
            'metadata' => ['nullable', 'array'],
        ]);
    }

    private function assertLinkableTeam(string $model, int $id, int $teamId): void
    {
        if (! $model::where('team_id', $teamId)->whereKey($id)->exists()) {
            throw ValidationException::withMessages(['linkable_id' => ['The selected record does not belong to this organisation.']]);
        }
    }
}
