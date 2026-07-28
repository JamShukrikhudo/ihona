<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PortalIntegration extends Model
{
    use BelongsToTeam, HasFactory, SoftDeletes;

    protected $fillable = [
        'team_id', 'branch_id', 'provider', 'country', 'channel',
        'sync_frequency', 'credentials', 'settings', 'active',
        'last_synced_at', 'last_sync_status', 'last_error',
    ];

    protected $hidden = ['credentials'];

    protected $casts = [
        'credentials' => 'encrypted:array',
        'settings' => 'array',
        'active' => 'boolean',
        'last_synced_at' => 'datetime',
    ];

    public function listings()
    {
        return $this->hasMany(PortalListing::class);
    }

    public function runs()
    {
        return $this->hasMany(PortalSyncRun::class);
    }
}
