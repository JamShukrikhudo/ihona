<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Lettings\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Liberu\RealEstate\Lettings\Domain\RentalApplicationStatus;

final class RentalApplication extends Model
{
    use SoftDeletes;

    protected $table = 'real_estate_rental_applications';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'status' => RentalApplicationStatus::class,
            'annual_income' => 'decimal:2',
            'monthly_income' => 'decimal:2',
            'application_date' => 'date',
            'desired_move_in_date' => 'date',
            'lease_end_date' => 'date',
            'guarantors' => 'array',
            'employer_reference' => 'array',
            'landlord_reference' => 'array',
            'screening_consent_at' => 'datetime',
            'submitted_at' => 'datetime',
            'decided_at' => 'datetime',
        ];
    }

    public function scopeForTeam(Builder $query, int|string $teamId): Builder
    {
        return $query->where('team_id', $teamId);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->whereIn('status', [RentalApplicationStatus::Submitted->value, RentalApplicationStatus::UnderReview->value]);
    }

    public function isPending(): bool
    {
        return in_array($this->status, [RentalApplicationStatus::Submitted, RentalApplicationStatus::UnderReview], true);
    }

    public function isScreeningComplete(): bool
    {
        return collect(['background_check_status', 'credit_report_status', 'rental_history_status', 'affordability_status', 'right_to_rent_status'])
            ->every(fn (string $field): bool => filled($this->{$field}) && $this->{$field} !== 'pending');
    }

    public function isScreeningPassed(): bool
    {
        return $this->isScreeningComplete()
            && ! in_array('failed', [$this->background_check_status, $this->credit_report_status, $this->rental_history_status, $this->affordability_status, $this->right_to_rent_status], true)
            && in_array($this->affordability_status, ['passed', 'not_required'], true)
            && in_array($this->right_to_rent_status, ['verified', 'not_required'], true);
    }
}
