<?php
declare(strict_types=1);
namespace Liberu\RealEstate\Valuations\Application;
use Illuminate\Validation\ValidationException;
final class CalculateCostOfMoving {
    public function handle(float $propertyValue, bool $isFirstTimeBuyer = false, float $movingDistance = 0): array {
        if ($propertyValue <= 0 || $movingDistance < 0) throw ValidationException::withMessages(['property_value' => 'Property value and distance must be valid.']);
        $stampDuty = $isFirstTimeBuyer && $propertyValue <= 425000 ? 0.0 : max(0.0, $propertyValue - 250000) * .05;
        return ['estimated' => true, 'property_value' => $propertyValue, 'stamp_duty' => $stampDuty, 'total_cost' => round($stampDuty + 1500 + 500 + 1295 + $movingDistance * 50, 2), 'disclaimer' => 'Estimate only; actual moving costs vary.'];
    }
}
