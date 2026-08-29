<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Properties\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Liberu\RealEstate\Core\Models\Branch;
use Liberu\RealEstate\Properties\Domain\PropertyStatus;

final class Property extends Model
{
    use SoftDeletes;

    public const EARLIEST_YEAR_BUILT = 1066;

    public const TYPES = [
        'residential' => 'Residential',
        'commercial' => 'Commercial',
        'land' => 'Land',
        'new_build' => 'New build',
        'development' => 'Development',
        'mixed_use' => 'Mixed use',
        'house' => 'House',
        'apartment' => 'Apartment',
        'condo' => 'Condo',
        'townhouse' => 'Townhouse',
        'villa' => 'Villa',
        'hmo' => 'HMO',
    ];

    protected $table = 'real_estate_properties';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'status' => PropertyStatus::class,
            'characteristics' => 'array',
            'utilities' => 'array',
            'features' => 'array',
            'structured_address' => 'array',
            'epc' => 'array',
            'floor_plan_data' => 'array',
            'price' => 'decimal:2',
            'area_sqft' => 'decimal:2',
            'service_charge' => 'decimal:2',
            'ground_rent' => 'decimal:2',
            'latitude' => 'float',
            'longitude' => 'float',
            'last_synced_at' => 'datetime',
            'description_generated_at' => 'datetime',
            'published_at' => 'datetime',
            'list_date' => 'date',
            'sold_date' => 'date',
            'is_featured' => 'boolean',
            'live_tour_available' => 'boolean',
            'ar_tour_enabled' => 'boolean',
            'ar_tour_settings' => 'array',
            'holographic_metadata' => 'array',
            'holographic_enabled' => 'boolean',
            'walkability_updated_at' => 'datetime',
            'energy_rating_date' => 'date',
            'insurance_expiry_date' => 'date',
            'ar_model_scale' => 'float',
        ];
    }

    public function setYearBuiltAttribute(mixed $value): void
    {
        $this->attributes['year_built'] = is_string($value) ? substr($value, 0, 4) : $value;
    }

    public static function latestYearBuilt(): int
    {
        return (int) now()->year + 2;
    }

    /** @return list<string> */
    public static function yearBuiltRules(): array
    {
        return ['integer', 'min:'.self::EARLIEST_YEAR_BUILT, 'max:'.self::latestYearBuilt()];
    }

    public static function yearBuiltMessage(): string
    {
        return __('Enter a build year between :from and :to.', [
            'from' => self::EARLIEST_YEAR_BUILT,
            'to' => self::latestYearBuilt(),
        ]);
    }

    public function history(): HasMany
    {
        return $this->hasMany(PropertyHistory::class, 'property_id');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(PropertyFavorite::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PropertyCategory::class, 'property_category_id');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(PropertyTemplate::class, 'property_template_id');
    }

    public function scopeForTeam(Builder $query, int|string $teamId): Builder
    {
        return $query->where('team_id', $teamId);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        return $query->when($term !== '', function (Builder $query) use ($term): void {
            $like = '%'.$term.'%';
            $query->where(function (Builder $query) use ($like): void {
                $query->where('address', 'like', $like)
                    ->orWhere('title', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhere('postal_code', 'like', $like);
            });
        });
    }

    public function scopePostalCode(Builder $query, ?string $postalCode): Builder
    {
        $postalCode = trim((string) $postalCode);

        return $query->when($postalCode !== '', fn (Builder $query): Builder => $query->where('postal_code', 'like', $postalCode.'%'));
    }

    public function scopeNearby(Builder $query, float|int|string $latitude, float|int|string $longitude, float|int|string $radius): Builder
    {
        $latitude = (float) $latitude;
        $longitude = (float) $longitude;
        $radius = (float) $radius;

        if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180 || $radius <= 0) {
            throw new \InvalidArgumentException('Nearby search coordinates and radius are invalid.');
        }

        $table = $query->getModel()->getTable();
        $earthRadiusKilometers = 6371;
        $latitudeLiteral = sprintf('%.8F', $latitude);
        $longitudeLiteral = sprintf('%.8F', $longitude);
        $radiusLiteral = sprintf('%.8F', $radius);
        $distance = "($earthRadiusKilometers * acos(cos(radians($latitudeLiteral)) * cos(radians($table.latitude)) * cos(radians($table.longitude) - radians($longitudeLiteral)) + sin(radians($latitudeLiteral)) * sin(radians($table.latitude))))";

        return $query
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->select($table.'.*')
            ->selectRaw($distance.' as distance')
            ->whereRaw($distance.' <= '.$radiusLiteral)
            ->orderBy('distance');
    }

    public function scopeNeedsSyncing(Builder $query): Builder
    {
        return $query->where(function (Builder $query): void {
            $query->whereNull('last_synced_at')
                ->orWhereColumn('updated_at', '>', 'last_synced_at');
        });
    }

    /** @param array<int, string> $amenities */
    public function scopeHasAmenities(Builder $query, array $amenities): Builder
    {
        $amenities = array_values(array_filter(array_map(
            static fn (mixed $amenity): string => trim((string) $amenity),
            $amenities,
        ), static fn (string $amenity): bool => $amenity !== ''));

        foreach ($amenities as $amenity) {
            $query->whereJsonContains('features', $amenity);
        }

        return $query;
    }

    public function needsWalkabilityUpdate(): bool
    {
        return $this->walkability_updated_at === null
            || $this->walkability_updated_at->lt(now()->subDays(30));
    }

    public function hasVirtualTour(): bool
    {
        return $this->virtualTourEmbed() !== null;
    }

    public function getVirtualTourEmbed(): ?string
    {
        return $this->virtualTourEmbed();
    }

    public function hasHolographicTour(): bool
    {
        return (bool) $this->holographic_enabled && filled($this->holographic_tour_url);
    }

    public function isHmo(): bool
    {
        return strtolower((string) $this->property_type) === 'hmo';
    }

    public function hasActiveInsurance(): bool
    {
        return filled($this->insurance_policy_id)
            && $this->insurance_expiry_date !== null
            && $this->insurance_expiry_date->isFuture();
    }

    private function virtualTourEmbed(): ?string
    {
        $url = (string) $this->virtual_tour_url;
        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $allowedHosts = ['matterport.com', 'kuula.co', '3dvista.com', '3dv.st', 'seekbeak.com'];

        if ($scheme !== 'https' || ! filter_var($url, FILTER_VALIDATE_URL) || ! collect($allowedHosts)->contains(
            fn (string $allowed): bool => $host === $allowed || str_ends_with($host, '.'.$allowed)
        )) {
            return null;
        }

        return '<iframe width="100%" height="480" src="'.htmlspecialchars($url, ENT_QUOTES, 'UTF-8').'" frameborder="0" sandbox="allow-scripts allow-same-origin allow-presentation" referrerpolicy="no-referrer" allow="xr-spatial-tracking" allowfullscreen></iframe>';
    }

    public function scopePriceRange(Builder $query, mixed $minimum, mixed $maximum): Builder
    {
        return $query->when($minimum !== null && $minimum !== '', fn (Builder $query): Builder => $query->where('price', '>=', $minimum))
            ->when($maximum !== null && $maximum !== '', fn (Builder $query): Builder => $query->where('price', '<=', $maximum));
    }

    public function scopeBedrooms(Builder $query, mixed $minimum, mixed $maximum = null): Builder
    {
        return $query->when($minimum !== null && $minimum !== '', fn (Builder $query): Builder => $query->where('bedrooms', '>=', $minimum))
            ->when($maximum !== null && $maximum !== '', fn (Builder $query): Builder => $query->where('bedrooms', '<=', $maximum));
    }

    public function scopeBathrooms(Builder $query, mixed $minimum, mixed $maximum = null): Builder
    {
        return $query->when($minimum !== null && $minimum !== '', fn (Builder $query): Builder => $query->where('bathrooms', '>=', $minimum))
            ->when($maximum !== null && $maximum !== '', fn (Builder $query): Builder => $query->where('bathrooms', '<=', $maximum));
    }

    public function scopeAreaRange(Builder $query, mixed $minimum, mixed $maximum): Builder
    {
        return $query->when($minimum !== null && $minimum !== '', fn (Builder $query): Builder => $query->where('area_sqft', '>=', $minimum))
            ->when($maximum !== null && $maximum !== '', fn (Builder $query): Builder => $query->where('area_sqft', '<=', $maximum));
    }

    public function scopeYearBuiltRange(Builder $query, mixed $minimum, mixed $maximum): Builder
    {
        return $query->when($minimum !== null && $minimum !== '', fn (Builder $query): Builder => $query->where('year_built', '>=', $minimum))
            ->when($maximum !== null && $maximum !== '', fn (Builder $query): Builder => $query->where('year_built', '<=', $maximum));
    }

    public function scopePropertyType(Builder $query, ?string $type): Builder
    {
        $type = trim((string) $type);

        return $query->when($type !== '', fn (Builder $query): Builder => $query->whereIn('property_type', array_unique([
            $type,
            strtolower($type),
            strtoupper($type),
            ucfirst(strtolower($type)),
        ])));
    }

    public function scopeCategory(Builder $query, int|string|null $category): Builder
    {
        return $query->when($category !== null && $category !== '', fn (Builder $query): Builder => $query->where('property_category_id', $category));
    }

    public function scopeFavoritedBy(Builder $query, int|string $teamId, int|string $userId): Builder
    {
        return $query->whereHas('favorites', fn (Builder $favorites): Builder => $favorites
            ->where('team_id', $teamId)
            ->where('user_id', $userId));
    }

    public function similarProperties(int $limit = 3): Collection
    {
        if ($this->price === null || $this->bedrooms === null || $this->bathrooms === null) {
            return new Collection();
        }

        return self::query()
            ->forTeam($this->team_id)
            ->where('id', '!=', $this->getKey())
            ->where('property_type', $this->property_type)
            ->whereBetween('price', [(float) $this->price * 0.8, (float) $this->price * 1.2])
            ->whereBetween('bedrooms', [(int) $this->bedrooms - 1, (int) $this->bedrooms + 1])
            ->whereBetween('bathrooms', [(int) $this->bathrooms - 1, (int) $this->bathrooms + 1])
            ->limit(max(1, min($limit, 20)))
            ->get();
    }

    public function scopeCountry(Builder $query, ?string $country): Builder
    {
        return $query->when(filled($country), fn (Builder $query): Builder => $query->where('country', strtoupper((string) $country)));
    }

    public function scopeEnergyRating(Builder $query, ?string $rating): Builder
    {
        return $query->when(filled($rating), fn (Builder $query): Builder => $query->where('energy_rating', strtoupper((string) $rating)));
    }

    public function scopeMinimumScore(Builder $query, string $column, mixed $minimum): Builder
    {
        abort_unless(in_array($column, ['energy_score', 'walkability_score', 'transit_score', 'bike_score'], true), 400);

        return $query->when($minimum !== null && $minimum !== '', fn (Builder $query): Builder => $query->where($column, '>=', $minimum));
    }

    public function scopeMinEnergyScore(Builder $query, mixed $minimum): Builder
    {
        return $query->minimumScore('energy_score', $minimum);
    }

    public function scopeWalkabilityScore(Builder $query, mixed $minimum): Builder
    {
        return $query->minimumScore('walkability_score', $minimum);
    }

    public function scopeTransitScore(Builder $query, mixed $minimum): Builder
    {
        return $query->minimumScore('transit_score', $minimum);
    }

    public function scopeBikeScore(Builder $query, mixed $minimum): Builder
    {
        return $query->minimumScore('bike_score', $minimum);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function canBePublished(): bool
    {
        return filled($this->address) && $this->status === PropertyStatus::Draft;
    }
}
