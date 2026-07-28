<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTeam;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CalendarEntry extends Model
{
    use BelongsToTeam, SoftDeletes;

    protected $fillable = [
        'team_id', 'branch_id', 'property_id', 'organiser_id', 'created_by',
        'type', 'title', 'description', 'location', 'starts_at', 'ends_at',
        'reminder_at', 'all_day', 'status', 'attendee_user_ids', 'contact_ids',
        'recurrence',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'reminder_at' => 'datetime',
        'all_day' => 'boolean',
        'attendee_user_ids' => 'array',
        'contact_ids' => 'array',
        'recurrence' => 'array',
    ];
}
