<?php

declare(strict_types=1);

namespace Liberu\RealEstate\OnTheMarket\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Liberu\RealEstate\OnTheMarket\Domain\OnTheMarketSyncStatus;

final class OnTheMarketSync extends Model
{
    use SoftDeletes;

    protected $table = 'real_estate_onthemarket_syncs';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['status' => OnTheMarketSyncStatus::class, 'payload' => 'array', 'last_synced_at' => 'datetime'];
    }

    public function scopeForTeam(Builder $query, int|string $teamId): Builder
    {
        return $query->where('team_id', $teamId);
    }
}
