<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganisationProfile extends Model
{
    use BelongsToTeam, HasFactory;

    protected $fillable = [
        'team_id', 'agency_name', 'logo_path', 'branding', 'email', 'phone',
        'address', 'operating_countries', 'primary_country', 'currency',
        'locale', 'language', 'timezone', 'date_format', 'measurement_system',
        'area_unit', 'setup_completed_at',
    ];

    protected function casts(): array
    {
        return [
            'branding' => 'array',
            'address' => 'array',
            'operating_countries' => 'array',
            'setup_completed_at' => 'datetime',
        ];
    }

    public function isComplete(): bool
    {
        return $this->setup_completed_at !== null;
    }
}
