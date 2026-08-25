<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Matching\Application;

use Illuminate\Validation\ValidationException;

final class CalculateMatchScore
{
    /** @param array<string, mixed> $criteria @param array<string, mixed> $property @return array<string, mixed> */
    public function handle(array $criteria, array $property): array
    {
        if (! array_key_exists('price', $property)) {
            throw ValidationException::withMessages(['property.price' => 'A property price is required for matching.']);
        }

        $scores = [
            'price_match' => $this->rangeScore($criteria, (float) $property['price'], 'min_price', 'max_price'),
            'location_match' => $this->locationScore($criteria, $property),
            'size_match' => $this->sizeScore($criteria, $property),
            'features_match' => $this->featuresScore($criteria, $property),
            'type_match' => $this->exactScore($criteria['property_type'] ?? null, $property['property_type'] ?? null),
            'school_match' => $this->collectionScore($criteria['required_schools'] ?? [], $property['schools'] ?? []),
            'transport_match' => $this->minimumScore($criteria['min_transit_score'] ?? null, $property['transit_score'] ?? null),
        ];
        $overall = $scores['price_match'] * 0.20 + $scores['location_match'] * 0.20 + $scores['size_match'] * 0.15 + $scores['features_match'] * 0.15 + $scores['type_match'] * 0.10 + $scores['school_match'] * 0.10 + $scores['transport_match'] * 0.10;

        return array_merge(array_map(fn (float|int $score): float => round((float) $score, 2), $scores), ['match_score' => round($overall, 2)]);
    }

    /** @param array<string, mixed> $criteria */
    private function rangeScore(array $criteria, float $value, string $minimumKey, string $maximumKey): float
    {
        if (! isset($criteria[$minimumKey]) && ! isset($criteria[$maximumKey])) {
            return 50.0;
        }
        $minimum = (float) ($criteria[$minimumKey] ?? 0);
        $maximum = (float) ($criteria[$maximumKey] ?? max($value, $minimum + 1));
        if ($value >= $minimum && $value <= $maximum) {
            return 100.0;
        }
        $boundary = $value < $minimum ? $minimum : $maximum;

        return $boundary > 0 ? max(0, 100 - abs($value - $boundary) / $boundary * 100) : 0.0;
    }

    /** @param array<string, mixed> $criteria @param array<string, mixed> $property */
    private function locationScore(array $criteria, array $property): float
    {
        $checks = [];
        if (filled($criteria['location'] ?? null)) {
            $checks[] = str_contains(strtolower((string) ($property['location'] ?? '')), strtolower((string) $criteria['location'])) ? 100 : 0;
        }
        if (! empty($criteria['postal_codes'])) {
            $checks[] = in_array($property['postal_code'] ?? null, $criteria['postal_codes'], true) ? 100 : 0;
        }

        return $checks === [] ? 50.0 : array_sum($checks) / count($checks);
    }

    /** @param array<string, mixed> $criteria @param array<string, mixed> $property */
    private function sizeScore(array $criteria, array $property): float
    {
        $checks = [];
        foreach ([['min_bedrooms', 'bedrooms', 'gte'], ['max_bedrooms', 'bedrooms', 'lte'], ['min_bathrooms', 'bathrooms', 'gte'], ['max_bathrooms', 'bathrooms', 'lte'], ['min_area', 'area_sqft', 'gte'], ['max_area', 'area_sqft', 'lte']] as [$criterion, $field, $operator]) {
            if (isset($criteria[$criterion])) {
                $checks[] = $operator === 'gte'
                    ? (float) ($property[$field] ?? 0) >= (float) $criteria[$criterion]
                    : (float) ($property[$field] ?? 0) <= (float) $criteria[$criterion];
            }
        }

        return $checks === [] ? 50.0 : (float) (count(array_filter($checks)) / count($checks) * 100);
    }

    /** @param array<string, mixed> $criteria @param array<string, mixed> $property */
    private function featuresScore(array $criteria, array $property): float
    {
        $required = array_values($criteria['required_features'] ?? []);
        if ($required === []) {
            return 50.0;
        }
        $available = array_values($property['features'] ?? []);

        return count(array_intersect($required, $available)) / count($required) * 100;
    }

    private function exactScore(mixed $expected, mixed $actual): float
    {
        return $expected === null ? 50.0 : ($expected === $actual ? 100.0 : 0.0);
    }

    /** @param array<int, mixed> $required @param array<int, mixed> $available */
    private function collectionScore(array $required, array $available): float
    {
        return $required === [] ? 50.0 : count(array_intersect(array_map('strtolower', $required), array_map('strtolower', $available))) / count($required) * 100;
    }

    private function minimumScore(mixed $minimum, mixed $actual): float
    {
        return $minimum === null ? 50.0 : ($actual === null ? 0.0 : min(100.0, (float) $actual / max(1, (float) $minimum) * 100));
    }
}
