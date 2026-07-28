<?php

namespace App\Models;

use App\Services\LetsSafeScreeningService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentalApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'tenant_id',
        'team_id',
        'applicant_id',
        'status',
        'employment_status',
        'annual_income',
        'monthly_income',
        'application_date',
        'desired_move_in_date',
        'guarantors',
        'employer_reference',
        'landlord_reference',
        'background_check_status',
        'credit_report_status',
        'rental_history_status',
        'affordability_status',
        'right_to_rent_status',
        'screening_consent_at',
        'submitted_at',
        'decided_at',
        'decided_by',
        'decision_notes',
        'smart_contract_address',
    ];

    protected $casts = [
        'annual_income' => 'decimal:2',
        'monthly_income' => 'decimal:2',
        'application_date' => 'date',
        'desired_move_in_date' => 'date',
        'guarantors' => 'array',
        'employer_reference' => 'array',
        'landlord_reference' => 'array',
        'screening_consent_at' => 'datetime',
        'submitted_at' => 'datetime',
        'decided_at' => 'datetime',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'applicant_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function decidedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function updateStatus($status)
    {
        $this->update(['status' => $status]);
    }

    public function initiateScreening()
    {
        $screeningService = new LetsSafeScreeningService;
        $screeningResult = $screeningService->screenTenant($this->tenant_id);

        if ($screeningResult) {
            $this->credit_report_status = $this->interpretCreditScore($screeningResult['credit_score']);
            $this->background_check_status = $screeningResult['background_check'];
            $this->rental_history_status = $screeningResult['rental_history'];
            $this->save();
        }
    }

    protected function interpretCreditScore($score)
    {
        if ($score === null) {
            return null;
        }
        if ($score >= 700) {
            return 'excellent';
        }
        if ($score >= 650) {
            return 'good';
        }
        if ($score >= 600) {
            return 'fair';
        }

        return 'poor';
    }

    public function isScreeningComplete()
    {
        return $this->background_check_status !== null
            && $this->credit_report_status !== null
            && $this->rental_history_status !== null
            && $this->affordability_status !== null
            && $this->right_to_rent_status !== null;
    }

    public function isScreeningPassed()
    {
        return in_array($this->background_check_status, ['passed', 'not_required'], true)
            && in_array($this->credit_report_status, ['excellent', 'good', 'fair', 'not_required'], true)
            && in_array($this->rental_history_status, ['good', 'satisfactory', 'not_available'], true)
            && $this->affordability_status === 'passed'
            && in_array($this->right_to_rent_status, ['verified', 'not_required'], true);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
