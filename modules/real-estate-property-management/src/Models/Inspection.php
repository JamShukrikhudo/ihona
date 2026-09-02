<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertyManagement\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Liberu\RealEstate\PropertyManagement\Domain\InspectionStatus;
use Liberu\RealEstate\PropertyManagement\Domain\InspectionType;

final class Inspection extends Model
{
    use SoftDeletes;

    protected $table = 'real_estate_inspections';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'type' => InspectionType::class,
            'status' => InspectionStatus::class,
            'scheduled_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'areas' => 'array',
            'photos' => 'array',
            'damage_reports' => 'array',
            'signatures' => 'array',
        ];
    }

    public function scopeForTeam(Builder $query, int|string $teamId): Builder
    {
        return $query->where('team_id', $teamId);
    }
}
