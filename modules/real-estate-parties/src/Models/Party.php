<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Parties\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Liberu\RealEstate\Parties\Domain\PartyType;

final class Party extends Model
{
    use SoftDeletes;

    protected $table = 'real_estate_parties';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'type' => PartyType::class,
            'metadata' => 'array',
            'consent_at' => 'datetime',
        ];
    }

    public function scopeForTeam(Builder $query, int|string $teamId): Builder
    {
        return $query->where('team_id', $teamId);
    }
}
