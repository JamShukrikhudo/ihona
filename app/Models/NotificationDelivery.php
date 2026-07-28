<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTeam;
use Illuminate\Database\Eloquent\Model;

class NotificationDelivery extends Model
{
    use BelongsToTeam;

    protected $fillable = [
        'team_id', 'user_id', 'event_type', 'channel', 'status', 'provider',
        'title', 'body', 'context', 'queued_at', 'sent_at', 'failed_at', 'error',
    ];

    protected $casts = [
        'context' => 'array',
        'queued_at' => 'datetime',
        'sent_at' => 'datetime',
        'failed_at' => 'datetime',
    ];
}
