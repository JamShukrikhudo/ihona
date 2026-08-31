<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

final class Terminology extends Model
{
    protected $table = 'real_estate_terminology';

    protected $guarded = ['id'];

    public function scopeForTeam(Builder $query, int|string $teamId): Builder
    {
        return $query->where('team_id', $teamId);
    }
}
