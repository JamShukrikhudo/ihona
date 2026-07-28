<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingSyncRun extends Model
{
    use BelongsToTeam, HasFactory;

    protected $fillable = [
        'team_id', 'accounting_integration_id', 'requested_by', 'status',
        'processed', 'succeeded', 'failed', 'errors', 'started_at', 'completed_at',
    ];

    protected $casts = [
        'errors' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];
}
