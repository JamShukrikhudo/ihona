<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subject',
        'content',
        'team_id',
        'created_by',
        'audience_filters',
        'status',
        'recipients_count',
        'delivered_count',
        'opened_count',
        'clicked_count',
        'scheduled_at',
        'sent_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'audience_filters' => 'array',
    ];

    public function leads()
    {
        return $this->belongsToMany(Lead::class, 'email_campaign_leads');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
