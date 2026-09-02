<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Properties\Application;

use Liberu\RealEstate\Properties\Models\PropertyPriceAlert;

final class DeletePriceAlert
{
    public function handle(int|string $teamId, int|string $userId, int|string $alertId): bool
    {
        return PropertyPriceAlert::query()->forUser($teamId, $userId)->findOrFail($alertId)->delete();
    }
}
