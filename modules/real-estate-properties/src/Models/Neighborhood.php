<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Properties\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Neighborhood extends Model
{
    use SoftDeletes;

    protected $table = 'real_estate_neighborhoods';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['schools' => 'array', 'amenities' => 'array', 'last_updated' => 'datetime'];
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(NeighborhoodReview::class);
    }

    public function scopeForTeam(Builder $query, int|string $teamId): Builder
    {
        return $query->where('team_id', $teamId);
    }

    public function averageReviewRating(): float
    {
        return round((float) ($this->reviews()->approved()->avg('rating') ?? 0), 2);
    }

    public function approvedReviewCount(): int
    {
        return $this->reviews()->approved()->count();
    }
}
