<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceIntegration extends Model
{
    use BelongsToTeam, HasFactory, SoftDeletes;

    protected $fillable = [
        'team_id', 'category', 'provider', 'name', 'credentials', 'settings',
        'active', 'is_default', 'last_checked_at', 'last_check_status', 'last_error',
    ];

    protected $hidden = ['credentials'];

    protected $casts = [
        'credentials' => 'encrypted:array',
        'settings' => 'array',
        'active' => 'boolean',
        'is_default' => 'boolean',
        'last_checked_at' => 'datetime',
    ];
}
