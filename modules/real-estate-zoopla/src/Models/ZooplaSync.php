<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Zoopla\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Liberu\RealEstate\Zoopla\Domain\ZooplaSyncStatus;

final class ZooplaSync extends Model
{
    use SoftDeletes;

    protected $table = 'real_estate_zoopla_syncs';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['status' => ZooplaSyncStatus::class, 'payload' => 'array', 'last_synced_at' => 'datetime'];
    }

    public function scopeForTeam(Builder $query, int|string $teamId): Builder
    {
        return $query->where('team_id', $teamId);
    }
}
