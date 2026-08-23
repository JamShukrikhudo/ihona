<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Listings\Application;

use Liberu\RealEstate\Listings\Models\Listing;

final class DeleteListing
{
    public function handle(Listing $listing, int|string $teamId): void
    {
        abort_unless((string) $listing->team_id === (string) $teamId, 404);
        $listing->delete();
    }
}
