<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inspection extends Model
{
    use BelongsToTeam, HasFactory, SoftDeletes;

    protected $fillable = [
        'team_id', 'branch_id', 'property_id', 'tenant_id', 'assigned_to',
        'created_by', 'type', 'status', 'scheduled_at', 'started_at',
        'completed_at', 'notes', 'areas', 'photos', 'damage_reports', 'signatures',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'areas' => 'array',
            'photos' => 'array',
            'damage_reports' => 'array',
            'signatures' => 'array',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
