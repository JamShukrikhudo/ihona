<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountingIntegration extends Model
{
    use BelongsToTeam, HasFactory, SoftDeletes;

    protected $fillable = [
        'team_id', 'provider', 'name', 'credentials', 'settings', 'active',
        'last_synced_at', 'last_sync_status', 'last_error',
    ];

    protected $hidden = ['credentials'];

    protected $casts = [
        'credentials' => 'encrypted:array',
        'settings' => 'array',
        'active' => 'boolean',
        'last_synced_at' => 'datetime',
    ];

    public function links()
    {
        return $this->hasMany(AccountingLink::class);
    }
}
