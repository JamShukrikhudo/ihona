<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutomationRun extends Model
{
    use BelongsToTeam, HasFactory;

    protected $fillable = [
        'team_id', 'automation_rule_id', 'status', 'context', 'results',
        'error', 'started_at', 'completed_at',
    ];

    protected $casts = [
        'context' => 'array',
        'results' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function automationRule()
    {
        return $this->belongsTo(AutomationRule::class);
    }
}
