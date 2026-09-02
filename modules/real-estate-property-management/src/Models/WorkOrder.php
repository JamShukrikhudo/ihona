<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertyManagement\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Liberu\RealEstate\PropertyManagement\Domain\WorkOrderStatus;

final class WorkOrder extends Model
{
    use SoftDeletes;

    protected $table = 'real_estate_work_orders';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['status' => WorkOrderStatus::class, 'scheduled_date' => 'datetime', 'started_date' => 'datetime', 'completed_date' => 'datetime', 'estimated_cost' => 'decimal:2', 'actual_cost' => 'decimal:2', 'estimated_hours' => 'decimal:2', 'actual_hours' => 'decimal:2', 'materials_cost' => 'decimal:2', 'labor_cost' => 'decimal:2', 'emergency_job' => 'boolean', 'requires_access' => 'boolean', 'safety_requirements' => 'array'];
    }

    public function updates(): HasMany
    {
        return $this->hasMany(WorkOrderUpdate::class);
    }

    public function scopeForTeam(Builder $query, int|string $teamId): Builder
    {
        return $query->where('team_id', $teamId);
    }

    public function isOverdue(): bool
    {
        return $this->scheduled_date?->isPast() === true && ! in_array($this->status, [WorkOrderStatus::Completed, WorkOrderStatus::Cancelled], true);
    }
}
