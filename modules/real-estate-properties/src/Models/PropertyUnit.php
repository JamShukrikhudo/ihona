<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Properties\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class PropertyUnit extends Model
{
    use SoftDeletes;

    protected $table = 'real_estate_property_units';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['characteristics' => 'array', 'area_sqft' => 'decimal:2'];
    }

    public function scopeForTeam(Builder $query, int|string $teamId): Builder
    {
        return $query->where('team_id', $teamId);
    }
}
