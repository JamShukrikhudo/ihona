<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyValuation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'property_id',
        'valuation_type',
        'estimated_value',
        'market_value',
        'rental_value',
        'valuation_date',
        'scheduled_at',
        'valuer_name',
        'valuer_company',
        'valuation_method',
        'comparable_properties',
        'market_conditions',
        'property_condition',
        'location_factors',
        'notes',
        'confidence_level',
        'valid_until',
        'team_id',
        'user_id',
        'assigned_to',
        'follow_up_at',
        'completed_at',
        'status',
        'outcome',
        'outcome_notes',
    ];

    protected $casts = [
        'valuation_date' => 'date',
        'scheduled_at' => 'datetime',
        'follow_up_at' => 'datetime',
        'completed_at' => 'datetime',
        'valid_until' => 'date',
        'estimated_value' => 'decimal:2',
        'market_value' => 'decimal:2',
        'rental_value' => 'decimal:2',
        'comparable_properties' => 'array',
        'location_factors' => 'array',
        'confidence_level' => 'integer',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function isValid(): bool
    {
        return $this->valid_until >= now();
    }

    public function getValuationAccuracy(): string
    {
        if ($this->confidence_level >= 90) {
            return 'High';
        } elseif ($this->confidence_level >= 70) {
            return 'Medium';
        } else {
            return 'Low';
        }
    }

    public function scopeValid($query)
    {
        return $query->where('valid_until', '>=', now());
    }

    public function scopeByType($query, $type)
    {
        return $query->where('valuation_type', $type);
    }
}
