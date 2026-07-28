<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTeam;
use Illuminate\Database\Eloquent\Model;

class NotificationPreference extends Model
{
    use BelongsToTeam;

    protected $fillable = [
        'team_id', 'user_id', 'channels', 'phone', 'push_tokens', 'event_preferences',
    ];

    protected $hidden = ['push_tokens'];

    protected $casts = [
        'channels' => 'array',
        'push_tokens' => 'encrypted:array',
        'event_preferences' => 'array',
    ];
}
