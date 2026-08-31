<?php

declare(strict_types=1);

namespace Liberu\RealEstate\ListingsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Liberu\RealEstate\Listings\Application\CreateListing;
use Liberu\RealEstate\Listings\Application\DeleteListing;
use Liberu\RealEstate\Listings\Application\TransitionListing;
use Liberu\RealEstate\Listings\Application\UpdateListing;
use Liberu\RealEstate\Listings\Application\UpdateListingSection;
use Liberu\RealEstate\Listings\Domain\ListingSection;
use Liberu\RealEstate\Listings\Domain\ListingStatus;
use Liberu\RealEstate\Listings\Models\Listing;
use Liberu\RealEstate\ListingsApi\Http\Resources\ListingResource;

final class ListingController
{
    public function index(Request $request): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless($teamId !== null, 403);
        $size = max(1, min($request->integer('page_size', 25), 100));

        return ListingResource::collection(Listing::query()->forTeam($teamId)->latest()->paginate($size))->response();
    }

    public function store(Request $request, CreateListing $create): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->current_team_id !== null, 403);
        $data = $request->validate(['title' => ['required', 'string', 'max:255'], 'property_id' => ['nullable', 'integer'], 'price' => ['nullable', 'numeric', 'min:0'], 'available_from' => ['nullable', 'date'], 'channel_content' => ['sometimes', 'array'], 'publication_rules' => ['sometimes', 'array'], 'portal_feeds' => ['sometimes', 'array'], 'reconciliation' => ['sometimes', 'array']]);

        return (new ListingResource($create->handle($user->current_team_id, $user->getAuthIdentifier(), $data)))->response()->setStatusCode(201);
    }

    public function show(Request $request, Listing $listing): JsonResponse
    {
        abort_unless((string) $request->user()?->current_team_id === (string) $listing->team_id, 404);

        return (new ListingResource($listing))->response();
    }

    public function update(Request $request, Listing $listing, UpdateListing $update): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless((string) $teamId === (string) $listing->team_id, 404);
        $data = $request->validate(['title' => ['sometimes', 'string', 'max:255'], 'price' => ['nullable', 'numeric', 'min:0'], 'available_from' => ['nullable', 'date'], 'channel_content' => ['sometimes', 'array'], 'publication_rules' => ['sometimes', 'array'], 'portal_feeds' => ['sometimes', 'array'], 'reconciliation' => ['sometimes', 'array']]);

        return (new ListingResource($update->handle($listing, $teamId, $data)))->response();
    }

    public function destroy(Request $request, Listing $listing, DeleteListing $delete): Response
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless((string) $teamId === (string) $listing->team_id, 404);
        $delete->handle($listing, $teamId);

        return response()->noContent();
    }

    public function transition(Request $request, Listing $listing, string $status, TransitionListing $transition): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->current_team_id !== null && (string) $user->current_team_id === (string) $listing->team_id, 404);
        $target = ListingStatus::tryFrom($status);
        abort_unless($target !== null, 404);
        $data = $request->validate([
            'channel_content' => ['sometimes', 'array'],
            'publication_rules' => ['sometimes', 'array'],
            'portal_feeds' => ['sometimes', 'array'],
            'reconciliation' => ['sometimes', 'array'],
        ]);

        return (new ListingResource($transition->handle($listing, $user->current_team_id, $target, $data)))->response();
    }

    public function updateSection(Request $request, Listing $listing, string $section, UpdateListingSection $update): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless((string) $teamId === (string) $listing->team_id, 404);
        $data = $request->validate(['value' => ['required', 'array']]);

        return (new ListingResource($update->handle($listing, $teamId, ListingSection::from($section), $data['value'])))->response();
    }
}
