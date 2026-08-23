<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Listings\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Listings\Domain\ListingStatus;
use Liberu\RealEstate\Listings\Models\Listing;

final class CreateListing
{
    public function handle(int|string $teamId, int|string $actorId, array $attributes): Listing
    {
        $title = trim((string) ($attributes['title'] ?? ''));
        if ($title === '') {
            throw ValidationException::withMessages(['title' => 'A listing title is required.']);
        }

return DB::transaction(fn (): Listing => Listing::query()->create(['team_id' => $teamId, 'created_by' => $actorId, 'property_id' => $attributes['property_id'] ?? null, 'title' => $title, 'status' => ListingStatus::Draft, 'price' => $attributes['price'] ?? null, 'available_from' => $attributes['available_from'] ?? null, 'channel_content' => $attributes['channel_content'] ?? [], 'publication_rules' => $attributes['publication_rules'] ?? [], 'portal_feeds' => $attributes['portal_feeds'] ?? [], 'reconciliation' => $attributes['reconciliation'] ?? []]));
    }
}
