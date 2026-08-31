<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Properties\Application;

use Liberu\RealEstate\Properties\Models\PropertyPriceAlert;

final class TogglePriceAlert
{
    public function handle(int|string $teamId, int|string $userId, int|string $alertId): PropertyPriceAlert
    {
        $alert = PropertyPriceAlert::query()->forUser($teamId, $userId)->findOrFail($alertId);
        $alert->update(['is_active' => ! $alert->is_active]);

        return $alert->refresh();
    }
}
