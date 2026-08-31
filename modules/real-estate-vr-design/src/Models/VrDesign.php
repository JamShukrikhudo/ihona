<?php

declare(strict_types=1);

namespace Liberu\RealEstate\VrDesign\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class VrDesign extends Model
{
    use SoftDeletes;

    protected $table = 'real_estate_vr_designs';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['design_data' => 'array', 'room_layout' => 'array', 'furniture_items' => 'array', 'materials' => 'array', 'lighting' => 'array', 'is_public' => 'boolean', 'is_template' => 'boolean', 'view_count' => 'integer'];
    }

    public function scopeForTeam(Builder $query, int|string $teamId): Builder
    {
        return $query->where('team_id', $teamId);
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }

    public function scopeTemplates(Builder $query): Builder
    {
        return $query->where('is_template', true);
    }

    public function scopeByStyle(Builder $query, string $style): Builder
    {
        return $query->where('style', $style);
    }

    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }
}
