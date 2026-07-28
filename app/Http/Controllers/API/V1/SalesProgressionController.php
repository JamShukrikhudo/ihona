<?php

namespace App\Http\Controllers\API\V1;

use App\Models\SalesProgression;
use App\Models\SalesProgressionEvent;
use App\Services\SalesProgressionService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SalesProgressionController extends TenantCrudController
{
    protected string $model = SalesProgression::class;

    protected string $routeParameter = 'sales_progression';

    protected array $searchable = ['buyer_solicitor_name', 'seller_solicitor_name', 'notes'];

    protected array $filterable = ['property_id', 'agent_id', 'stage'];

    public function __construct(private readonly SalesProgressionService $progressions) {}

    protected function rules(Request $request, ?Model $record = null): array
    {
        $teamId = $this->teamId($request);

        return [
            'property_id' => [$record ? 'sometimes' : 'required', Rule::exists('properties', 'id')->where('team_id', $teamId)],
            'transaction_id' => ['nullable', Rule::exists('transactions', 'id')->where('team_id', $teamId)],
            'agent_id' => ['nullable', Rule::exists('team_user', 'user_id')->where('team_id', $teamId)],
            'stage' => [$record ? 'prohibited' : 'sometimes', Rule::in(array_keys(SalesProgression::STAGES))],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'offer_accepted_date' => ['nullable', 'date'],
            'exchange_date' => ['nullable', 'date'],
            'completion_date' => ['nullable', 'date'],
            'buyer_solicitor_name' => ['nullable', 'string', 'max:255'],
            'buyer_solicitor_email' => ['nullable', 'email'],
            'buyer_solicitor_phone' => ['nullable', 'string', 'max:50'],
            'seller_solicitor_name' => ['nullable', 'string', 'max:255'],
            'seller_solicitor_email' => ['nullable', 'email'],
            'seller_solicitor_phone' => ['nullable', 'string', 'max:50'],
            'mortgage_lender' => ['nullable', 'string', 'max:255'],
            'mortgage_broker' => ['nullable', 'string', 'max:255'],
            'checklist_items' => [$record ? 'prohibited' : 'nullable', 'array'],
            'notes' => ['nullable', 'string'],
        ];
    }

    protected function prepareForCreate(Request $request, array $attributes): array
    {
        $attributes['stage'] ??= 'offer_accepted';
        $attributes['offer_accepted_date'] ??= now()->toDateString();
        $attributes['checklist_items'] ??= SalesProgressionService::DEFAULT_CHECKLIST;

        return $attributes;
    }

    public function store(Request $request): JsonResponse
    {
        $response = parent::store($request);
        $progression = SalesProgression::findOrFail($response->getData(true)['data']['id']);

        $this->recordEvent($request, $progression, [
            'event_type' => 'progression_created',
            'to_stage' => $progression->stage,
            'summary' => 'Sales progression created.',
        ]);

        return $response;
    }

    public function timeline(Request $request, int $salesProgression): JsonResponse
    {
        $progression = $this->record($request);

        return response()->json([
            'data' => $progression->events()
                ->with('recordedBy:id,name,email')
                ->latest('occurred_at')
                ->latest('id')
                ->get(),
        ]);
    }

    public function stage(Request $request, int $salesProgression): JsonResponse
    {
        /** @var SalesProgression $progression */
        $progression = $this->record($request);
        $attributes = $request->validate([
            'stage' => ['required', Rule::in(array_keys(SalesProgression::STAGES))],
            'effective_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($attributes['stage'] === 'exchanged' && ! ($attributes['effective_date'] ?? $progression->exchange_date)) {
            throw ValidationException::withMessages([
                'effective_date' => ['An effective date is required when recording exchange.'],
            ]);
        }
        if ($attributes['stage'] === 'completed' && ! ($attributes['effective_date'] ?? $progression->completion_date)) {
            throw ValidationException::withMessages([
                'effective_date' => ['An effective date is required when recording completion.'],
            ]);
        }

        $fromStage = $progression->stage;
        DB::transaction(function () use ($request, $progression, $attributes, $fromStage): void {
            $updates = ['stage' => $attributes['stage']];
            if ($attributes['stage'] === 'exchanged') {
                $updates['exchange_date'] = $attributes['effective_date'] ?? $progression->exchange_date;
            }
            if ($attributes['stage'] === 'completed') {
                $updates['completion_date'] = $attributes['effective_date'] ?? $progression->completion_date;
            }

            $progression->update($updates);
            $this->synchroniseSaleState($progression);
            $this->recordEvent($request, $progression, [
                'event_type' => 'stage_changed',
                'from_stage' => $fromStage,
                'to_stage' => $progression->stage,
                'summary' => $attributes['notes'] ?? 'Sales stage changed to '.$progression->stage_label.'.',
                'metadata' => ['effective_date' => $attributes['effective_date'] ?? null],
            ]);
        });

        return response()->json(['data' => $progression->fresh()]);
    }

    public function checklist(Request $request, int $salesProgression, string $item): JsonResponse
    {
        /** @var SalesProgression $progression */
        $progression = $this->record($request);
        $attributes = $request->validate([
            'completed' => ['required', 'boolean'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            $progression = $this->progressions->updateChecklistItem($progression, $item, $attributes['completed']);
        } catch (\InvalidArgumentException $exception) {
            throw ValidationException::withMessages(['item' => [$exception->getMessage()]]);
        }

        $this->recordEvent($request, $progression, [
            'event_type' => 'checklist_updated',
            'summary' => $attributes['notes'] ?? sprintf(
                'Checklist item %s marked %s.',
                str_replace('_', ' ', $item),
                $attributes['completed'] ? 'complete' : 'incomplete'
            ),
            'metadata' => ['item' => $item, 'completed' => $attributes['completed']],
        ]);

        return response()->json([
            'data' => $progression,
            'completion_percentage' => $this->progressions->getChecklistCompletionPercentage($progression),
        ]);
    }

    public function memorandum(Request $request, int $salesProgression): JsonResponse
    {
        /** @var SalesProgression $progression */
        $progression = $this->record($request);
        $attributes = $request->validate([
            'issued_at' => ['required', 'date'],
            'reference' => ['nullable', 'string', 'max:100'],
            'recipients' => ['required', 'array', 'min:1'],
            'recipients.*' => ['email'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            $progression = $this->progressions->updateChecklistItem($progression, 'memorandum_sent', true);
        } catch (\InvalidArgumentException) {
            // Custom checklists can omit the standard memorandum item.
        }

        $event = $this->recordEvent($request, $progression, [
            'event_type' => 'memorandum_issued',
            'summary' => $attributes['notes'] ?? 'Memorandum of sale issued.',
            'metadata' => [
                'issued_at' => $attributes['issued_at'],
                'reference' => $attributes['reference'] ?? null,
                'recipients' => $attributes['recipients'],
            ],
            'occurred_at' => $attributes['issued_at'],
        ]);

        return response()->json(['data' => $event], 201);
    }

    private function synchroniseSaleState(SalesProgression $progression): void
    {
        $propertyUpdates = match ($progression->stage) {
            'offer_accepted' => ['status' => 'under_offer'],
            'exchanged' => ['status' => 'exchanged'],
            'completed' => ['status' => 'sold', 'sold_date' => $progression->completion_date],
            default => [],
        };

        if ($propertyUpdates !== []) {
            $progression->property()->update($propertyUpdates);
        }

        if ($progression->transaction) {
            $progression->transaction->update([
                'status' => $progression->stage === 'completed' ? 'completed' : 'in_progress',
                'completed_at' => $progression->stage === 'completed' ? $progression->completion_date : null,
            ]);
        }
    }

    private function recordEvent(Request $request, SalesProgression $progression, array $attributes): SalesProgressionEvent
    {
        return $progression->events()->create(array_merge([
            'team_id' => $this->teamId($request),
            'recorded_by' => $request->user()->id,
            'occurred_at' => now(),
        ], $attributes));
    }
}
