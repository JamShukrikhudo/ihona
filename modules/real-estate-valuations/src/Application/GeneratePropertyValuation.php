<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Valuations\Application;

use Illuminate\Validation\ValidationException;

/**
 * Produces a deterministic, explainable valuation estimate from property facts.
 * The result is deliberately labelled as an estimate; it is not a professional appraisal.
 */
final class GeneratePropertyValuation
{
    public const MODEL_VERSION = '1.0.0';

    /** @param array<string, mixed> $property */
    /** @return array<string, mixed> */
    public function handle(array $property, int $comparablesCount = 0, int $trainingSamples = 0): array
    {
        $area = (float) ($property['area_sqft'] ?? 0);
        $bedrooms = (int) ($property['bedrooms'] ?? 0);
        $bathrooms = (int) ($property['bathrooms'] ?? 0);
        $yearBuilt = (int) ($property['year_built'] ?? 0);

        if ($area <= 0 || $bedrooms < 0 || $bathrooms < 0 || $yearBuilt < 1000 || $trainingSamples < 0 || $comparablesCount < 0) {
            throw ValidationException::withMessages(['property' => 'Property area, rooms, year, and valuation counts must be valid.']);
        }

        $features = $this->features($property, $area, $bedrooms, $bathrooms, $yearBuilt);
        $weights = $this->weights();
        $rawValue = $weights['base_value'];

        foreach ($weights['features'] as $feature => $weight) {
            $rawValue += $features[$feature] * $weight;
        }

        $marketAdjustment = 1.05;
        $estimatedValue = max(0.0, $rawValue) * $marketAdjustment;
        $confidence = $this->confidence($property, $features, $trainingSamples, $comparablesCount);
        $featureImportance = $this->featureImportance($features, $weights['features']);
        $bandWidth = max(0.05, min(0.20, (100 - $confidence) / 100));

        return [
            'estimated' => true,
            'estimated_value' => round($estimatedValue, 2),
            'confidence_level' => $confidence,
            'price_range' => [
                'min' => round($estimatedValue * (1 - $bandWidth), 2),
                'max' => round($estimatedValue * (1 + $bandWidth), 2),
            ],
            'method' => 'explainable_heuristic',
            'model_version' => self::MODEL_VERSION,
            'feature_importance' => $featureImportance,
            'comparables_count' => $comparablesCount,
            'training_samples' => $trainingSamples,
            'market_trend' => $this->marketTrend((string) ($property['address'] ?? $property['location'] ?? 'default')),
            'prediction_factors' => $this->predictionFactors($features),
            'disclaimer' => 'Estimate only; this is not a professional appraisal or financial advice.',
        ];
    }

    /** @param array<string, mixed> $property */
    /** @return array<string, float> */
    private function features(array $property, float $area, int $bedrooms, int $bathrooms, int $yearBuilt): array
    {
        $age = max(0, now()->year - $yearBuilt);
        $price = (float) ($property['price'] ?? 0);
        $type = (string) ($property['property_type'] ?? '');
        $status = (string) ($property['status'] ?? '');

        return [
            'bedrooms' => $bedrooms,
            'bathrooms' => $bathrooms,
            'area_sqft' => $area,
            'year_built' => $yearBuilt,
            'age' => $age,
            'latitude' => (float) ($property['latitude'] ?? 0),
            'longitude' => (float) ($property['longitude'] ?? 0),
            'is_detached' => $type === 'detached' ? 1 : 0,
            'is_semi_detached' => $type === 'semi-detached' ? 1 : 0,
            'is_apartment' => $type === 'apartment' ? 1 : 0,
            'is_townhouse' => $type === 'townhouse' ? 1 : 0,
            'is_for_sale' => in_array($status, ['for_sale', 'available'], true) ? 1 : 0,
            'is_for_rent' => in_array($status, ['for_rent', 'let'], true) ? 1 : 0,
            'is_featured' => ! empty($property['is_featured']) ? 1 : 0,
            'days_on_market' => isset($property['list_date']) ? max(0, (int) now()->diffInDays($property['list_date'])) : 0,
            'price_per_sqft' => $area > 0 ? $price / $area : 0,
        ];
    }

    /** @return array{base_value: float, features: array<string, float>} */
    private function weights(): array
    {
        return [
            'base_value' => 100000,
            'features' => [
                'bedrooms' => 15000,
                'bathrooms' => 12000,
                'area_sqft' => 150,
                'year_built' => -50,
                'age' => -500,
                'is_detached' => 50000,
                'is_semi_detached' => 30000,
                'is_apartment' => 10000,
                'is_townhouse' => 25000,
                'is_featured' => 20000,
                'days_on_market' => -100,
                'latitude' => 5000,
                'longitude' => -3000,
            ],
        ];
    }

    /** @param array<string, mixed> $property @param array<string, float> $features */
    private function confidence(array $property, array $features, int $trainingSamples, int $comparablesCount): int
    {
        $confidence = 100;
        foreach (['bedrooms', 'bathrooms', 'area_sqft', 'year_built', 'property_type'] as $field) {
            if (empty($property[$field])) {
                $confidence -= 10;
            }
        }
        if ($features['age'] > 100 || $features['age'] < 0) {
            $confidence -= 15;
        }
        if ($features['area_sqft'] < 300 || $features['area_sqft'] > 10000) {
            $confidence -= 10;
        }
        if ($trainingSamples <= 10) {
            $confidence -= 20;
        }
        $confidence += min(10, $comparablesCount * 2);

        return max(0, min(100, $confidence));
    }

    /** @param array<string, float> $features @param array<string, float> $weights @return array<string, float> */
    private function featureImportance(array $features, array $weights): array
    {
        $impact = [];
        $total = 0.0;
        foreach ($weights as $feature => $weight) {
            $value = abs($features[$feature] * $weight);
            $impact[$feature] = $value;
            $total += $value;
        }
        if ($total > 0) {
            foreach ($impact as $feature => $value) {
                $impact[$feature] = round($value / $total * 100, 2);
            }
        }
        arsort($impact);

        return array_slice($impact, 0, 5, true);
    }

    /** @param array<string, float> $features @return list<string> */
    private function predictionFactors(array $features): array
    {
        $factors = [];
        if ($features['area_sqft'] > 2000) {
            $factors[] = 'Large property size adds significant value';
        }
        if ($features['age'] < 10) {
            $factors[] = 'New construction premium applied';
        } elseif ($features['age'] > 50) {
            $factors[] = 'Age of property reduces value';
        }
        if ($features['is_detached'] === 1) {
            $factors[] = 'Detached property type adds premium';
        }
        if ($features['is_featured'] === 1) {
            $factors[] = 'Featured property status indicates higher quality';
        }
        if ($features['days_on_market'] > 90) {
            $factors[] = 'Extended time on market may indicate overpricing';
        }

        return $factors;
    }

    private function marketTrend(string $seed): string
    {
        $trends = ['rising', 'stable', 'declining', 'volatile'];

        return $trends[abs(crc32($seed)) % count($trends)];
    }
}
