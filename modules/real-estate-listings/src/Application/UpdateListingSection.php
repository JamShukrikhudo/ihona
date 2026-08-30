<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Listings\Application;

use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Listings\Domain\ListingSection;
use Liberu\RealEstate\Listings\Models\Listing;

final class UpdateListingSection
{
    /** @param array<string, mixed> $value */
    public function handle(Listing $listing, int|string $teamId, ListingSection $section, array $value): Listing
    {
        if ((string) $listing->team_id !== (string) $teamId) {
            throw ValidationException::withMessages(['listing' => 'The listing does not belong to this team.']);
        }
        $listing->forceFill([$section->value => $value])->save();

        return $listing->refresh();
    }
}
