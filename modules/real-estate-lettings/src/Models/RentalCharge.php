<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Lettings\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Liberu\RealEstate\Lettings\Domain\RentalChargeStatus;

final class RentalCharge extends Model
{
    use SoftDeletes;

    protected $table = 'real_estate_rental_charges';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['status' => RentalChargeStatus::class, 'amount' => 'decimal:2', 'charge_date' => 'date'];
    }

    public function scopeForTeam(Builder $query, int|string $teamId): Builder
    {
        return $query->where('team_id', $teamId);
    }
}
