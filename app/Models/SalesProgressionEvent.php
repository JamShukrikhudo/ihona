<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesProgressionEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'sales_progression_id',
        'event_type',
        'from_stage',
        'to_stage',
        'summary',
        'metadata',
        'recorded_by',
        'occurred_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'occurred_at' => 'datetime',
    ];

    public function progression(): BelongsTo
    {
        return $this->belongsTo(SalesProgression::class, 'sales_progression_id');
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
