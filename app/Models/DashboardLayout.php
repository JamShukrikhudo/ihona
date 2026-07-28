<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DashboardLayout extends Model
{
    protected $fillable = ['team_id', 'user_id', 'name', 'widgets', 'is_default'];

    protected function casts(): array
    {
        return [
            'widgets' => 'array',
            'is_default' => 'boolean',
        ];
    }
}
