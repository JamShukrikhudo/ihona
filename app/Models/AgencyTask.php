<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgencyTask extends Model
{
    use BelongsToTeam, HasFactory, SoftDeletes;

    protected $table = 'agency_tasks';

    protected $fillable = [
        'team_id', 'branch_id', 'assigned_to', 'created_by', 'taskable_type',
        'taskable_id', 'title', 'description', 'priority', 'status', 'due_at',
        'completed_at', 'checklist',
    ];

    protected function casts(): array
    {
        return [
            'due_at' => 'datetime',
            'completed_at' => 'datetime',
            'checklist' => 'array',
        ];
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function taskable(): MorphTo
    {
        return $this->morphTo();
    }
}
