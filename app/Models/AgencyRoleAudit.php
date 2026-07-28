<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTeam;
use Illuminate\Database\Eloquent\Model;

class AgencyRoleAudit extends Model
{
    use BelongsToTeam;

    public const UPDATED_AT = null;

    protected $fillable = [
        'team_id', 'actor_id', 'subject_id', 'action', 'old_role', 'new_role',
        'old_permissions', 'new_permissions', 'ip_address', 'user_agent',
    ];

    protected $casts = [
        'old_permissions' => 'array',
        'new_permissions' => 'array',
        'created_at' => 'datetime',
    ];

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function subject()
    {
        return $this->belongsTo(User::class, 'subject_id');
    }
}
