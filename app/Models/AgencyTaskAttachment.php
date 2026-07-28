<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgencyTaskAttachment extends Model
{
    use BelongsToTeam, HasFactory;

    protected $fillable = [
        'team_id', 'agency_task_id', 'uploaded_by', 'name', 'path', 'mime_type', 'size',
    ];
}
