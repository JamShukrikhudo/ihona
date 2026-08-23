<?php

declare(strict_types=1);

namespace Liberu\RealEstate\SalesProgression\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Liberu\RealEstate\SalesProgression\Domain\SalesProgressionStatus;

final class SalesProgression extends Model
{
    use SoftDeletes;

    protected $table = 'real_estate_sales_progressions';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['status' => SalesProgressionStatus::class, 'milestones' => 'array', 'chain' => 'array', 'professionals' => 'array', 'completion_controls' => 'array', 'exchanged_at' => 'datetime', 'completed_at' => 'datetime'];
    }

    public function scopeForTeam(Builder $query, int|string $teamId): Builder
    {
        return $query->where('team_id', $teamId);
    }
}
