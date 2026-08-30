<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Parties\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class PartyRelationship extends Model
{
    use SoftDeletes;

    protected $table = 'real_estate_party_relationships';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }

    public function scopeForTeam(Builder $query, int|string $teamId): Builder
    {
        return $query->where('team_id', $teamId);
    }
}
