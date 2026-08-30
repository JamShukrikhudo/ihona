<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

final class AuditEntry extends Model
{
    public $timestamps = false;

    protected $table = 'real_estate_audit_entries';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['metadata' => 'array', 'created_at' => 'datetime'];
    }

    public function scopeForTeam(Builder $query, int|string $teamId): Builder
    {
        return $query->where('team_id', $teamId);
    }
}
