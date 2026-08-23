<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Properties\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Liberu\RealEstate\Properties\Domain\PropertyStatus;

final class Property extends Model
{
    use SoftDeletes;

    protected $table = 'real_estate_properties';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'status' => PropertyStatus::class,
            'characteristics' => 'array',
            'utilities' => 'array',
            'features' => 'array',
            'published_at' => 'datetime',
        ];
    }

    public function history(): HasMany
    {
        return $this->hasMany(PropertyHistory::class, 'property_id');
    }

    public function scopeForTeam($query, int|string $teamId)
    {
        return $query->where('team_id', $teamId);
    }

    public function canBePublished(): bool
    {
        return filled($this->address) && $this->status === PropertyStatus::Draft;
    }
}
