<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Properties\Application;

use Liberu\RealEstate\Properties\Models\PropertySavedSearch;

final class DeletePropertySearch
{
    public function handle(int|string $teamId, int|string $userId, int|string $searchId): bool
    {
        return PropertySavedSearch::query()->forUser($teamId, $userId)->whereKey($searchId)->delete() > 0;
    }
}
