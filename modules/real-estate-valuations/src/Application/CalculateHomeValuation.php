<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Valuations\Application;

use Illuminate\Validation\ValidationException;

final class CalculateHomeValuation
{
    /** @return array<string, mixed> */
    public function handle(float $propertySize, int $bedrooms, int $bathrooms, int $yearBuilt, string $propertyType, string $condition, string $location, float $basePrice = 3000): array
    {
        if ($propertySize <= 0 || $bedrooms < 0 || $bathrooms < 0 || $yearBuilt < 1000 || $basePrice <= 0) {
            throw ValidationException::withMessages(['property' => 'Property size, rooms, year, and base price must be valid positive values.']);
        }

        $baseValue = $propertySize * $basePrice;
        $typeMultiplier = ['detached' => 1.3, 'semi-detached' => 1.1, 'terraced' => 1.0, 'apartment' => 0.9, 'bungalow' => 1.15][$propertyType] ?? 1.0;
        $conditionMultiplier = ['excellent' => 1.2, 'good' => 1.1, 'fair' => 1.0, 'poor' => 0.85][$condition] ?? 1.0;
        $locationMultiplier = ['prime' => 1.4, 'good' => 1.2, 'average' => 1.0, 'below-average' => 0.8][$location] ?? 1.0;
        $age = max(0, (int) date('Y') - $yearBuilt);
        $ageAdjustment = $age <= 5 ? 1.1 : ($age <= 10 ? 1.05 : ($age <= 30 ? 1.0 : ($age <= 50 ? 0.95 : 0.9)));
        $roomBonus = max(0, $bedrooms - 2) * 15000 + max(0, $bathrooms - 1) * 8000;
        $estimated = $baseValue * $typeMultiplier * $conditionMultiplier * $locationMultiplier * $ageAdjustment + $roomBonus;
        $confidence = 85 + (in_array($propertyType, ['apartment', 'terraced'], true) ? 5 : 0) + (in_array($condition, ['excellent', 'good'], true) ? 3 : ($condition === 'poor' ? -5 : 0)) + ($location === 'prime' ? 5 : ($location === 'below-average' ? -3 : 0));
        $confidence = max(70, min(95, $confidence));

        return ['estimated_value' => round($estimated, 2), 'min_value' => round($estimated * 0.9, 2), 'max_value' => round($estimated * 1.1, 2), 'confidence_level' => $confidence, 'property_size' => $propertySize, 'bedrooms' => $bedrooms, 'bathrooms' => $bathrooms, 'year_built' => $yearBuilt, 'property_age' => $age, 'property_type' => $propertyType, 'condition' => $condition, 'location' => $location, 'base_price_per_unit' => $basePrice, 'breakdown' => ['base_value' => round($baseValue, 2), 'type_multiplier' => $typeMultiplier, 'condition_multiplier' => $conditionMultiplier, 'location_multiplier' => $locationMultiplier, 'age_adjustment' => $ageAdjustment, 'room_bonus' => round($roomBonus, 2)]];
    }
}
