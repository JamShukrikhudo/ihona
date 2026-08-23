<?php

declare(strict_types=1);

namespace Liberu\RealEstate\OffersApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Liberu\RealEstate\Offers\Application\CreateOffer;
use Liberu\RealEstate\Offers\Application\DeleteOffer;
use Liberu\RealEstate\Offers\Application\UpdateOffer;
use Liberu\RealEstate\Offers\Models\Offer;

final class OfferController
{
    public function index(Request $request): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless($teamId !== null, 403);
        $size = max(1, min($request->integer('page_size', 25), 100));

        return response()->json(['data' => Offer::query()->forTeam($teamId)->latest()->paginate($size)]);
    }

    public function store(Request $request, CreateOffer $create): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->current_team_id !== null, 403);
        $data = $request->validate(['subject' => ['required', 'string', 'max:255'], 'amount' => ['required', 'numeric', 'min:0'], 'property_id' => ['nullable', 'integer'], 'party_id' => ['nullable', 'integer'], 'terms' => ['sometimes', 'array'], 'qualification' => ['sometimes', 'array'], 'negotiation' => ['sometimes', 'array'], 'proof' => ['sometimes', 'array'], 'decision_history' => ['sometimes', 'array'], 'accepted_controls' => ['sometimes', 'array']]);

        return response()->json(['data' => $create->handle($user->current_team_id, $user->getAuthIdentifier(), $data)], 201);
    }

    public function show(Request $request, Offer $offer): JsonResponse
    {
        abort_unless((string) $request->user()?->current_team_id === (string) $offer->team_id, 404);

        return response()->json(['data' => $offer]);
    }

    public function update(Request $request, Offer $offer, UpdateOffer $update): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless((string) $teamId === (string) $offer->team_id, 404);
        $data = $request->validate(['subject' => ['sometimes', 'string', 'max:255'], 'amount' => ['sometimes', 'numeric', 'min:0'], 'status' => ['sometimes', 'string', 'in:draft,submitted,countered,accepted,rejected,withdrawn'], 'terms' => ['sometimes', 'array'], 'qualification' => ['sometimes', 'array'], 'negotiation' => ['sometimes', 'array'], 'proof' => ['sometimes', 'array'], 'decision_history' => ['sometimes', 'array'], 'accepted_controls' => ['sometimes', 'array']]);

        return response()->json(['data' => $update->handle($offer, $teamId, $data)]);
    }

    public function destroy(Request $request, Offer $offer, DeleteOffer $delete): Response
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless((string) $teamId === (string) $offer->team_id, 404);
        $delete->handle($offer, $teamId);

        return response()->noContent();
    }
}
