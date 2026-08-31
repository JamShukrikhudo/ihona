<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertyManagement\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Liberu\RealEstate\PropertyManagement\Domain\MaintenancePriority;
use Liberu\RealEstate\PropertyManagement\Domain\MaintenanceStatus;

final class MaintenanceRequest extends Model
{
    use SoftDeletes;

    protected $table = 'real_estate_maintenance_requests';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['status' => MaintenanceStatus::class, 'priority' => MaintenancePriority::class, 'requested_date' => 'date', 'photos' => 'array', 'quote_references' => 'array', 'completed_at' => 'datetime'];
    }

    public function scopeForTeam(Builder $query, int|string $teamId): Builder
    {
        return $query->where('team_id', $teamId);
    }
}
