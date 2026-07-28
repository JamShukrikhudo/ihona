<?php

namespace App\Models;

use App\Services\PropertyFeatureService;
use Illuminate\Database\Eloquent\Model;

class PropertyFeature extends Model
{
    protected $primaryKey = 'feature_id';

    protected $fillable = [
        'team_id',
        'property_id',
        'feature_name',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($feature) {
            app(PropertyFeatureService::class)->clearCache();
        });

        static::updated(function ($feature) {
            app(PropertyFeatureService::class)->clearCache();
        });

        static::deleted(function ($feature) {
            app(PropertyFeatureService::class)->clearCache();
        });
    }
}
