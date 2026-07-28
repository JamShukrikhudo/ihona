<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Buyer;
use App\Models\Property;
use App\Models\PropertyMatch;
use App\Notifications\NewPropertyMatches;
use App\Services\PropertyMatchingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PropertyMatchController extends Controller
{
    public function __construct(private readonly PropertyMatchingService $matching)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $query = PropertyMatch::query()
            ->where('team_id', $this->teamId($request))
            ->with(['buyer', 'property.images'])
            ->when($request->filled('buyer_id'), fn ($query) => $query->where('buyer_id', $request->integer('buyer_id')))
            ->when($request->filled('property_id'), fn ($query) => $query->where('property_id', $request->integer('property_id')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('minimum_score'), fn ($query) => $query->where('match_score', '>=', $request->float('minimum_score')));

        return response()->json($query->orderByDesc('match_score')->paginate(
            min(max($request->integer('per_page', 20), 1), 100)
        ));
    }

    public function forBuyer(Request $request, int $buyer): JsonResponse
    {
        $record = Buyer::where('team_id', $this->teamId($request))->findOrFail($buyer);
        $matches = $this->matching->generateMatchesForBuyer($record);
        $matches->each->load('property');

        if ($record->user && $matches->isNotEmpty()) {
            $record->user->notify(new NewPropertyMatches($matches));
        }

        return response()->json(['data' => $matches, 'meta' => ['total' => $matches->count()]]);
    }

    public function forProperty(Request $request, int $property): JsonResponse
    {
        $record = Property::where('team_id', $this->teamId($request))->findOrFail($property);
        $matches = $this->matching->generateMatchesForProperty($record);
        $matches->each->load('buyer');

        $matches->groupBy('buyer.user_id')->each(function ($buyerMatches, $userId) {
            if ($userId && ($user = $buyerMatches->first()->buyer->user)) {
                $user->notify(new NewPropertyMatches($buyerMatches));
            }
        });

        return response()->json(['data' => $matches, 'meta' => ['total' => $matches->count()]]);
    }

    public function update(Request $request, int $propertyMatch): JsonResponse
    {
        $record = PropertyMatch::where('team_id', $this->teamId($request))->findOrFail($propertyMatch);
        $record->update($request->validate([
            'status' => ['sometimes', Rule::in(['active', 'dismissed', 'interested', 'viewed'])],
            'viewed_by_buyer' => ['sometimes', 'boolean'],
            'buyer_interest_level' => ['nullable', 'integer', 'between:1,5'],
            'agent_notes' => ['nullable', 'string'],
        ]) + ['last_updated' => now()]);

        return response()->json(['data' => $record->fresh()]);
    }

    private function teamId(Request $request): int
    {
        $user = $request->user();
        $teamId = $user?->current_team_id;
        if (! $teamId || ! $user->allTeams()->contains('id', $teamId)) {
            throw ValidationException::withMessages(['team' => ['Select an organisation you belong to first.']]);
        }
        return (int) $teamId;
    }
}
