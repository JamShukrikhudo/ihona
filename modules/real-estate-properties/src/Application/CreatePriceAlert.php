<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Properties\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Properties\Domain\PriceAlertFrequency;
use Liberu\RealEstate\Properties\Models\Property;
use Liberu\RealEstate\Properties\Models\PropertyPriceAlert;

final class CreatePriceAlert
{
    /** @param array<string, mixed> $data */
    public function handle(int|string $teamId, int|string $userId, int|string $propertyId, array $data): PropertyPriceAlert
    {
        $property = Property::query()->forTeam($teamId)->findOrFail($propertyId);
        $percentage = (float) ($data['alert_percentage'] ?? 0);
        $frequency = PriceAlertFrequency::tryFrom((string) ($data['alert_frequency'] ?? ''));

        if ($percentage < 0.1 || $percentage > 100) {
            throw ValidationException::withMessages(['alert_percentage' => 'The alert percentage must be between 0.1 and 100.']);
        }
        if ($frequency === null) {
            throw ValidationException::withMessages(['alert_frequency' => 'Select a daily, weekly, or monthly frequency.']);
        }
        if ($property->price === null) {
            throw ValidationException::withMessages(['property' => 'The property does not have a current price.']);
        }

        return DB::transaction(fn (): PropertyPriceAlert => PropertyPriceAlert::query()->create([
            'team_id' => $teamId,
            'user_id' => $userId,
            'property_id' => $property->getKey(),
            'initial_price' => $property->price,
            'alert_percentage' => $percentage,
            'alert_frequency' => $frequency,
            'is_active' => true,
        ]));
    }
}
