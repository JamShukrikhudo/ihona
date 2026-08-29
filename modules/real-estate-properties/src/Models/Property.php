<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Properties\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Liberu\RealEstate\Core\Models\Branch;
use Liberu\RealEstate\Properties\Domain\PropertyStatus;

final class Property extends Model
{
    use SoftDeletes;

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

    public function history(): HasMany
    {
        return $this->hasMany(PropertyHistory::class, 'property_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
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

    public function scopePropertyType(Builder $query, ?string $type): Builder
    {
        return $query->when(filled($type), fn (Builder $query): Builder => $query->where('property_type', $type));
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

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function canBePublished(): bool
    {
        return filled($this->address) && $this->status === PropertyStatus::Draft;
    }
}
