<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Offer;
use App\Models\User;
use App\Services\WorkflowNotifier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OfferController extends TenantCrudController
{
    protected string $model = Offer::class;

    protected string $routeParameter = 'offer';

    protected array $filterable = ['property_id', 'contact_id', 'status', 'negotiator_id'];

    public function __construct(private readonly WorkflowNotifier $notifications) {}

    protected function rules(Request $request, ?Model $record = null): array
    {
        $teamId = $this->teamId($request);

        return [
            'property_id' => [
                $record ? 'sometimes' : 'required',
                Rule::exists('properties', 'id')->where('team_id', $teamId),
            ],
            'contact_id' => [
                $record ? 'sometimes' : 'required',
                Rule::exists('contacts', 'id')->where('team_id', $teamId),
            ],
            'negotiator_id' => [
                'nullable',
                Rule::exists('team_user', 'user_id')->where('team_id', $teamId),
            ],
            'amount' => [$record ? 'sometimes' : 'required', 'numeric', 'min:0'],
            'currency' => ['sometimes', 'string', 'size:3'],
            'status' => ['sometimes', Rule::in(['pending', 'accepted', 'rejected', 'withdrawn', 'superseded'])],
            'mortgage_status' => ['nullable', Rule::in(['cash', 'agreement_in_principle', 'approved', 'required', 'unknown'])],
            'chain_information' => ['nullable', 'string'],
            'conditions' => ['nullable', 'string'],
            'offered_at' => [$record ? 'sometimes' : 'required', 'date'],
            'responded_at' => ['nullable', 'date'],
            'negotiation_note' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function store(Request $request): JsonResponse
    {
        $attributes = $request->validate($this->rules($request));
        $note = Arr::pull($attributes, 'negotiation_note');
        $attributes['team_id'] = $this->teamId($request);
        $attributes['currency'] ??= 'GBP';
        $attributes['status'] ??= 'pending';

        $offer = DB::transaction(function () use ($request, $attributes, $note): Offer {
            $offer = Offer::create($attributes);
            $this->recordEvent($request, $offer, null, 'created', $note);

            return $offer;
        });
        $this->notify($request, $offer, 'offer.created');

        return response()->json(['data' => $offer->fresh()], 201);
    }

    public function update(Request $request): JsonResponse
    {
        /** @var Offer $offer */
        $offer = $this->record($request);
        $attributes = $request->validate($this->rules($request, $offer));
        $note = Arr::pull($attributes, 'negotiation_note');
        $before = $offer->replicate();

        DB::transaction(function () use ($request, $offer, $attributes, $before, $note): void {
            $offer->update($attributes);

            if ($offer->wasChanged() || $note !== null) {
                $eventType = $offer->wasChanged('status') ? 'status_changed' : 'revised';
                $this->recordEvent($request, $offer, $before, $eventType, $note);
            }
        });
        if ($offer->wasChanged('status') && in_array($offer->status, ['accepted', 'rejected', 'withdrawn'], true)) {
            $this->notify($request, $offer, "offer.{$offer->status}");
        }

        return response()->json(['data' => $offer->fresh()]);
    }

    public function timeline(Request $request): JsonResponse
    {
        /** @var Offer $offer */
        $offer = $this->record($request);

        return response()->json([
            'data' => $offer->events()->with('actor:id,name,email')->get(),
        ]);
    }

    private function recordEvent(
        Request $request,
        Offer $offer,
        ?Offer $before,
        string $eventType,
        ?string $note
    ): void {
        $changes = $before === null
            ? Arr::only($offer->getAttributes(), [
                'amount', 'currency', 'status', 'mortgage_status',
                'chain_information', 'conditions', 'offered_at',
            ])
            : collect($offer->getChanges())
                ->except(['updated_at'])
                ->all();

        $offer->events()->create([
            'team_id' => $this->teamId($request),
            'actor_id' => $request->user()->id,
            'event_type' => $eventType,
            'previous_amount' => $before?->amount,
            'amount' => $offer->amount,
            'previous_status' => $before?->status,
            'status' => $offer->status,
            'conditions' => $offer->conditions,
            'note' => $note,
            'changes' => $changes,
            'occurred_at' => now(),
        ]);
    }

    private function notify(Request $request, Offer $offer, string $event): void
    {
        $recipient = User::find($offer->negotiator_id) ?? $request->user();
        $this->notifications->notify(
            $this->teamId($request),
            $recipient,
            $event,
            $event === 'offer.created' ? 'New property offer' : 'Offer '.ucfirst($offer->status),
            "{$offer->currency} {$offer->amount}",
            ['offer_id' => $offer->id, 'property_id' => $offer->property_id, 'status' => $offer->status],
        );
    }
}
