<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedReport extends Model
{
    protected $fillable = [
        'team_id', 'created_by', 'name', 'type', 'filters', 'columns',
        'chart_type', 'is_shared',
    ];

    protected function casts(): array
    {
        return [
            'filters' => 'array',
            'columns' => 'array',
            'is_shared' => 'boolean',
        ];
    }
}
