<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertyManagement\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Liberu\RealEstate\PropertyManagement\Domain\VendorQuoteStatus;

final class VendorQuote extends Model
{
    use SoftDeletes;

    protected $table = 'real_estate_vendor_quotes';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['status' => VendorQuoteStatus::class, 'quote_amount' => 'decimal:2', 'labor_cost' => 'decimal:2', 'materials_cost' => 'decimal:2', 'additional_costs' => 'decimal:2', 'quote_date' => 'date', 'valid_until' => 'date', 'start_date' => 'date', 'completion_date' => 'date'];
    }

    public function scopeForTeam(Builder $query, int|string $teamId): Builder
    {
        return $query->where('team_id', $teamId);
    }

    public function isValid(): bool
    {
        return $this->valid_until !== null && ! $this->valid_until->isPast();
    }

    public function totalCost(): float
    {
        return (float) $this->labor_cost + (float) $this->materials_cost + (float) $this->additional_costs;
    }
}
