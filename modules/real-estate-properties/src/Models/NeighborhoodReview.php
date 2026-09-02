<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Properties\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

final class NeighborhoodReview extends Model
{
    use SoftDeletes;

    protected $table = 'real_estate_neighborhood_reviews';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['approved' => 'boolean', 'review_date' => 'date'];
    }

    public function neighborhood(): BelongsTo
    {
        return $this->belongsTo(Neighborhood::class);
    }

    public function scopeForTeam(Builder $query, int|string $teamId): Builder
    {
        return $query->where('team_id', $teamId);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('approved', true)->where('moderation_status', 'approved');
    }

    public function scopeHighRated(Builder $query): Builder
    {
        return $query->where('rating', '>=', 4);
    }
}
