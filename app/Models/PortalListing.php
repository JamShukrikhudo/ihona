<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortalListing extends Model
{
    use BelongsToTeam, HasFactory;

    protected $fillable = [
        'team_id', 'portal_integration_id', 'property_id', 'status',
        'external_id', 'payload', 'published_at', 'last_synced_at', 'last_error',
    ];

    protected $casts = [
        'payload' => 'array',
        'published_at' => 'datetime',
        'last_synced_at' => 'datetime',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
