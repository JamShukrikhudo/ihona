<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Listings\Application;

use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Listings\Models\Listing;

final class UpdateListing
{
    public function handle(Listing $listing, int|string $teamId, array $attributes): Listing
    {
        abort_unless((string) $listing->team_id === (string) $teamId, 404);
        if (array_key_exists('title', $attributes) && trim((string) $attributes['title']) === '') {
            throw ValidationException::withMessages(['title' => 'A listing title is required.']);
        }
        if (array_key_exists('status', $attributes) || array_key_exists('published_at', $attributes)) {
            throw ValidationException::withMessages(['status' => 'Listing lifecycle changes must use the transition action.']);
        }

        $listing->fill($attributes);
        $listing->save();

        return $listing->fresh();
    }
}
