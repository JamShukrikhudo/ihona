<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

final class NumberSequence extends Model
{
    protected $table = 'real_estate_number_sequences';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['next_value' => 'integer', 'padding' => 'integer'];
    }

    public function scopeForTeam(Builder $query, int|string $teamId): Builder
    {
        return $query->where('team_id', $teamId);
    }
}
