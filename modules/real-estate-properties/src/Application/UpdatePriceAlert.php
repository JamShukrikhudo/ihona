<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Properties\Application;

use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Properties\Domain\PriceAlertFrequency;
use Liberu\RealEstate\Properties\Models\PropertyPriceAlert;

final class UpdatePriceAlert
{
    /** @param array<string, mixed> $data */
    public function handle(int|string $teamId, int|string $userId, int|string $alertId, array $data): PropertyPriceAlert
    {
        $alert = PropertyPriceAlert::query()->forUser($teamId, $userId)->findOrFail($alertId);
        $percentage = (float) ($data['alert_percentage'] ?? $alert->alert_percentage);
        $frequency = PriceAlertFrequency::tryFrom((string) ($data['alert_frequency'] ?? $alert->alert_frequency));

        if ($percentage < 0.1 || $percentage > 100) {
            throw ValidationException::withMessages(['alert_percentage' => 'The alert percentage must be between 0.1 and 100.']);
        }
        if ($frequency === null) {
            throw ValidationException::withMessages(['alert_frequency' => 'Select a daily, weekly, or monthly frequency.']);
        }

        $alert->update([
            'alert_percentage' => $percentage,
            'alert_frequency' => $frequency,
            'is_active' => $data['is_active'] ?? $alert->is_active,
        ]);

        return $alert->refresh();
    }
}
