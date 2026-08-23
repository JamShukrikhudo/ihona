<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Offers\Application;

use Liberu\RealEstate\Offers\Models\Offer;

final class DeleteOffer
{
    public function handle(Offer $offer, int|string $teamId): void
    {
        abort_unless((string) $offer->team_id === (string) $teamId, 404);
        $offer->delete();
    }
}
