<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Properties\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class CommunityEvent extends Model
{
    protected $table = 'real_estate_property_community_events';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'event_date' => 'datetime',
            'end_date' => 'datetime',
            'is_public' => 'boolean',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('event_date', '>=', now())->orderBy('event_date');
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }

    public function scopeForTeam(Builder $query, int|string $teamId): Builder
    {
        return $query->where(function (Builder $query) use ($teamId): void {
            $query->whereNull('team_id')->orWhere('team_id', $teamId);
        });
    }

    public function scopeCategory(Builder $query, ?string $category): Builder
    {
        return $query->when($category !== null && trim($category) !== '', fn (Builder $query): Builder => $query->where('category', trim($category)));
    }

    public function scopeNearby(Builder $query, float|int|string $latitude, float|int|string $longitude, float|int|string $radius = 10): Builder
    {
        $latitude = (float) $latitude;
        $longitude = (float) $longitude;
        $radius = (float) $radius;

        if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180 || $radius <= 0 || $radius > 500) {
            throw new \InvalidArgumentException('Nearby event coordinates and radius are invalid.');
        }

        $table = $this->getTable();
        $distance = sprintf('(6371 * acos(cos(radians(%.8F)) * cos(radians(%s.latitude)) * cos(radians(%s.longitude) - radians(%.8F)) + sin(radians(%.8F)) * sin(radians(%s.latitude))))', $latitude, $table, $table, $longitude, $latitude, $table);
        $latitudeDelta = $radius / 111.0;
        $longitudeDelta = $radius / (111.0 * max(cos(deg2rad($latitude)), 0.01));

        return $query->whereNotNull('latitude')->whereNotNull('longitude')
            ->whereBetween('latitude', [$latitude - $latitudeDelta, $latitude + $latitudeDelta])
            ->whereBetween('longitude', [$longitude - $longitudeDelta, $longitude + $longitudeDelta])
            ->select($table.'.*')->selectRaw($distance.' as distance')->whereRaw($distance.' <= ?', [$radius])->orderBy('distance');
    }
}
