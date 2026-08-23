<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Valuations\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Liberu\RealEstate\Valuations\Domain\ValuationStatus;

final class Valuation extends Model
{
    use SoftDeletes;

    protected $table = 'real_estate_valuations';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['status' => ValuationStatus::class, 'comparable_data' => 'array', 'recommendation' => 'array', 'fee_amount' => 'decimal:2', 'valued_amount' => 'decimal:2', 'scheduled_at' => 'datetime', 'completed_at' => 'datetime'];
    }

    public function scopeForTeam($query, int|string $teamId)
    {
        return $query->where('team_id', $teamId);
    }
}
