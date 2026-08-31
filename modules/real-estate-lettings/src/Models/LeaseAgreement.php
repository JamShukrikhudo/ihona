<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Lettings\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Liberu\RealEstate\Lettings\Domain\LeaseAgreementStatus;

final class LeaseAgreement extends Model
{
    use SoftDeletes;

    protected $table = 'real_estate_lease_agreements';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['status' => LeaseAgreementStatus::class, 'start_date' => 'date', 'end_date' => 'date', 'monthly_rent' => 'decimal:2', 'security_deposit' => 'decimal:2', 'is_signed' => 'boolean', 'landlord_signed' => 'boolean', 'tenant_signed' => 'boolean', 'contract_deployed_at' => 'datetime', 'notice_served_at' => 'date', 'notice_expires_at' => 'date', 'ended_at' => 'date'];
    }

    public function scopeForTeam(Builder $query, int|string $teamId): Builder
    {
        return $query->where('team_id', $teamId);
    }

    public function isFullySigned(): bool
    {
        return $this->landlord_signed && $this->tenant_signed;
    }

    public function canDeploySmartContract(): bool
    {
        return blank($this->smart_contract_address) && $this->start_date !== null && $this->end_date !== null && (float) $this->monthly_rent > 0;
    }
}
