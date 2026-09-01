<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PortalsReporting\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class SavedReport extends Model
{
    use SoftDeletes;

    protected $table = 'real_estate_saved_reports';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['filters' => 'array', 'is_shared' => 'boolean'];
    }

    public function scopeForTeam(Builder $q, int|string $id): Builder
    {
        return $q->where('team_id', $id);
    }

    public function visibleTo(int|string $userId): bool
    {
        return (string) $this->created_by === (string) $userId || (bool) $this->is_shared;
    }
}
