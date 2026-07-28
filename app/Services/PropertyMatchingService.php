<?php

namespace App\Services;

use App\Models\Buyer;
use App\Models\Property;
use App\Models\PropertyMatch;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class PropertyMatchingService
{
    public function findMatches(Buyer|Tenant $applicant, int $limit = 10): Collection
    {
        $criteria = $applicant->search_criteria ?? [];
        $query = Property::query()
            ->where('team_id', $applicant->team_id)
            ->whereIn('status', $this->availability($applicant, $criteria));

        $this->applyDatabaseCriteria($query, $criteria);

        $properties = $query->with(['features', 'neighborhood'])
            ->limit(min(max($limit * 20, 100), 1000))
            ->get()
            ->filter(fn (Property $property) => $this->meetsCalculatedCriteria($criteria, $property));

        return $this->calculateMatchScores($applicant, $properties)->take($limit);
    }

    public function calculateMatchScore(Buyer|Tenant $applicant, Property $property): array
    {
        $criteria = $applicant->search_criteria ?? [];
        $distance = $this->distanceFor($criteria, $property);
        $scores = [
            'price_match' => $this->calculatePriceMatch($criteria, (float) $property->price),
            'location_match' => $this->calculateLocationMatch($criteria, $property, $distance),
            'size_match' => $this->calculateSizeMatch($criteria, $property),
            'features_match' => $this->calculateFeaturesMatch($criteria, $property),
            'type_match' => $this->calculateTypeMatch($criteria, $property),
            'school_match' => $this->calculateSchoolMatch($criteria, $property),
            'transport_match' => $this->calculateTransportMatch($criteria, $property),
        ];

        $overallScore = (
            $scores['price_match'] * 0.20
            + $scores['location_match'] * 0.20
            + $scores['size_match'] * 0.15
            + $scores['features_match'] * 0.15
            + $scores['type_match'] * 0.10
            + $scores['school_match'] * 0.10
            + $scores['transport_match'] * 0.10
        );

        return collect($scores)
            ->map(fn (float $score) => round($score, 2))
            ->merge([
                'match_score' => round($overallScore, 2),
                'distance_km' => $distance === null ? null : round($distance, 2),
                'availability' => $property->status,
            ])->all();
    }

    public function createMatch(Buyer|Tenant $applicant, Property $property, array $scores): PropertyMatch
    {
        $applicantKey = $applicant instanceof Buyer ? 'buyer_id' : 'tenant_id';
        $otherKey = $applicant instanceof Buyer ? 'tenant_id' : 'buyer_id';

        return PropertyMatch::updateOrCreate([
            $applicantKey => $applicant->id,
            'property_id' => $property->id,
        ], array_merge([
            $otherKey => null,
            'team_id' => $applicant->team_id,
            'auto_generated' => true,
            'match_criteria' => $applicant->search_criteria,
            'last_updated' => now(),
        ], $scores));
    }

    public function generateMatchesForBuyer(Buyer $buyer): Collection
    {
        return $this->generateMatchesForApplicant($buyer);
    }

    public function generateMatchesForTenant(Tenant $tenant): Collection
    {
        return $this->generateMatchesForApplicant($tenant);
    }

    public function generateMatchesForProperty(Property $property): Collection
    {
        $buyers = Buyer::where('status', 'active')
            ->where('team_id', $property->team_id)
            ->whereNotNull('search_criteria')
            ->get();
        $tenants = Tenant::where('status', 'active')
            ->where('team_id', $property->team_id)
            ->whereNotNull('search_criteria')
            ->get();

        return $buyers->concat($tenants)
            ->filter(fn (Buyer|Tenant $applicant) => $this->propertyMatchesCriteria($applicant, $property))
            ->map(function (Buyer|Tenant $applicant) use ($property) {
                $scores = $this->calculateMatchScore($applicant, $property);

                return $scores['match_score'] >= 50
                    ? $this->createMatch($applicant, $property, $scores)
                    : null;
            })->filter()->values();
    }

    private function generateMatchesForApplicant(Buyer|Tenant $applicant): Collection
    {
        return $this->findMatches($applicant, 20)
            ->map(function (Property $property) use ($applicant) {
                $scores = $this->calculateMatchScore($applicant, $property);

                return $scores['match_score'] >= 50
                    ? $this->createMatch($applicant, $property, $scores)
                    : null;
            })->filter()->values();
    }

    private function propertyMatchesCriteria(Buyer|Tenant $applicant, Property $property): bool
    {
        $criteria = $applicant->search_criteria ?? [];

        if (! in_array($property->status, $this->availability($applicant, $criteria), true)) {
            return false;
        }

        $property->loadMissing(['features', 'neighborhood']);

        return $this->meetsSimpleCriteria($criteria, $property)
            && $this->meetsCalculatedCriteria($criteria, $property);
    }

    private function availability(Buyer|Tenant $applicant, array $criteria): array
    {
        return $criteria['availability'] ?? ($applicant instanceof Tenant
            ? ['to_let', 'available']
            : ['available']);
    }

    private function applyDatabaseCriteria(Builder $query, array $criteria): void
    {
        if (isset($criteria['min_price'])) {
            $query->where('price', '>=', $criteria['min_price']);
        }
        if (isset($criteria['max_price'])) {
            $query->where('price', '<=', $criteria['max_price']);
        }
        if (isset($criteria['property_type'])) {
            $query->where('property_type', $criteria['property_type']);
        }
        if (isset($criteria['min_bedrooms'])) {
            $query->where('bedrooms', '>=', $criteria['min_bedrooms']);
        }
        if (isset($criteria['max_bedrooms'])) {
            $query->where('bedrooms', '<=', $criteria['max_bedrooms']);
        }
        if (isset($criteria['min_bathrooms'])) {
            $query->where('bathrooms', '>=', $criteria['min_bathrooms']);
        }
        if (filled($criteria['location'] ?? null)) {
            $query->where('location', 'like', '%'.$criteria['location'].'%');
        }
        if (! empty($criteria['postal_codes'])) {
            $query->whereIn('postal_code', $criteria['postal_codes']);
        }
        foreach ($criteria['required_features'] ?? [] as $feature) {
            $query->whereHas('features', fn (Builder $query) => $query->where('feature_name', $feature));
        }
    }

    private function meetsSimpleCriteria(array $criteria, Property $property): bool
    {
        return (! isset($criteria['min_price']) || $property->price >= $criteria['min_price'])
            && (! isset($criteria['max_price']) || $property->price <= $criteria['max_price'])
            && (! isset($criteria['property_type']) || $property->property_type === $criteria['property_type'])
            && (! isset($criteria['min_bedrooms']) || $property->bedrooms >= $criteria['min_bedrooms'])
            && (! isset($criteria['max_bedrooms']) || $property->bedrooms <= $criteria['max_bedrooms'])
            && (! isset($criteria['min_bathrooms']) || $property->bathrooms >= $criteria['min_bathrooms'])
            && (! filled($criteria['location'] ?? null) || str_contains(mb_strtolower($property->location), mb_strtolower($criteria['location'])))
            && (empty($criteria['postal_codes']) || in_array($property->postal_code, $criteria['postal_codes'], true))
            && collect($criteria['required_features'] ?? [])->diff(
                $property->features->pluck('feature_name')
            )->isEmpty();
    }

    private function meetsCalculatedCriteria(array $criteria, Property $property): bool
    {
        $distance = $this->distanceFor($criteria, $property);
        if (isset($criteria['radius_km']) && ($distance === null || $distance > $criteria['radius_km'])) {
            return false;
        }
        if ($this->calculateSchoolMatch($criteria, $property) < 100 && ! empty($criteria['required_schools'])) {
            return false;
        }

        $transit = $this->transitScore($property);

        return ! isset($criteria['min_transit_score'])
            || ($transit !== null && $transit >= $criteria['min_transit_score']);
    }

    private function calculateMatchScores(Buyer|Tenant $applicant, Collection $properties): Collection
    {
        return $properties->map(function (Property $property) use ($applicant) {
            $scores = $this->calculateMatchScore($applicant, $property);
            $property->match_score = $scores['match_score'];
            $property->match_details = $scores;

            return $property;
        })->sortByDesc('match_score')->values();
    }

    private function calculatePriceMatch(array $criteria, float $price): float
    {
        if (! isset($criteria['min_price']) && ! isset($criteria['max_price'])) {
            return 50;
        }

        $min = (float) ($criteria['min_price'] ?? 0);
        $max = (float) ($criteria['max_price'] ?? max($price, $min + 1));
        if ($price >= $min && $price <= $max) {
            return 100;
        }

        $boundary = $price < $min ? $min : $max;

        return $boundary > 0 ? max(0, 100 - abs($price - $boundary) / $boundary * 100) : 0;
    }

    private function calculateLocationMatch(array $criteria, Property $property, ?float $distance): float
    {
        $scores = [];
        if (filled($criteria['location'] ?? null)) {
            $scores[] = str_contains(mb_strtolower($property->location), mb_strtolower($criteria['location'])) ? 100 : 0;
        }
        if (! empty($criteria['postal_codes'])) {
            $scores[] = in_array($property->postal_code, $criteria['postal_codes'], true) ? 100 : 0;
        }
        if (isset($criteria['radius_km'])) {
            $scores[] = $distance === null ? 0 : max(0, 100 * (1 - $distance / $criteria['radius_km']));
        }

        return $scores === [] ? 50 : array_sum($scores) / count($scores);
    }

    private function calculateSizeMatch(array $criteria, Property $property): float
    {
        $checks = [];
        foreach ([
            ['min_bedrooms', '>=', 'bedrooms'],
            ['max_bedrooms', '<=', 'bedrooms'],
            ['min_bathrooms', '>=', 'bathrooms'],
            ['min_area', '>=', 'area_sqft'],
            ['max_area', '<=', 'area_sqft'],
        ] as [$key, $operator, $field]) {
            if (isset($criteria[$key])) {
                $checks[] = $operator === '>='
                    ? $property->{$field} >= $criteria[$key]
                    : $property->{$field} <= $criteria[$key];
            }
        }

        return $checks === [] ? 50 : collect($checks)->filter()->count() / count($checks) * 100;
    }

    private function calculateFeaturesMatch(array $criteria, Property $property): float
    {
        $required = collect($criteria['required_features'] ?? []);
        if ($required->isEmpty()) {
            return 50;
        }

        return $required->intersect($property->features->pluck('feature_name'))->count()
            / $required->count() * 100;
    }

    private function calculateTypeMatch(array $criteria, Property $property): float
    {
        return isset($criteria['property_type'])
            ? ($criteria['property_type'] === $property->property_type ? 100 : 0)
            : 50;
    }

    private function calculateSchoolMatch(array $criteria, Property $property): float
    {
        $required = collect($criteria['required_schools'] ?? [])->map(fn ($name) => mb_strtolower($name));
        if ($required->isEmpty()) {
            return 50;
        }

        $schools = collect($property->neighborhood?->schools ?? [])
            ->map(fn ($school) => mb_strtolower(is_array($school)
                ? ($school['name'] ?? $school['school_name'] ?? '')
                : (string) $school));

        return $required->intersect($schools)->count() / $required->count() * 100;
    }

    private function calculateTransportMatch(array $criteria, Property $property): float
    {
        if (! isset($criteria['min_transit_score'])) {
            return 50;
        }
        $score = $this->transitScore($property);

        return $score === null ? 0 : min(100, $score / max(1, $criteria['min_transit_score']) * 100);
    }

    private function transitScore(Property $property): ?float
    {
        $score = $property->transit_score ?? $property->neighborhood?->transit_score;

        return $score === null ? null : (float) $score;
    }

    private function distanceFor(array $criteria, Property $property): ?float
    {
        if (! isset($criteria['latitude'], $criteria['longitude'], $criteria['radius_km'])
            || $property->latitude === null || $property->longitude === null) {
            return null;
        }

        $earthRadius = 6371;
        $latitudeDelta = deg2rad((float) $property->latitude - (float) $criteria['latitude']);
        $longitudeDelta = deg2rad((float) $property->longitude - (float) $criteria['longitude']);
        $originLatitude = deg2rad((float) $criteria['latitude']);
        $propertyLatitude = deg2rad((float) $property->latitude);
        $a = sin($latitudeDelta / 2) ** 2
            + cos($originLatitude) * cos($propertyLatitude) * sin($longitudeDelta / 2) ** 2;

        return $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
