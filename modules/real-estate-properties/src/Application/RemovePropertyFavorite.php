<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Properties\Application;

use Liberu\RealEstate\Properties\Models\PropertyFavorite;

final class RemovePropertyFavorite
{
    public function handle(int|string $teamId, int|string $userId, int|string $propertyId): bool
    {
        return PropertyFavorite::query()
            ->where('team_id', $teamId)
            ->where('user_id', $userId)
            ->where('property_id', $propertyId)
            ->delete() > 0;
    }
}
