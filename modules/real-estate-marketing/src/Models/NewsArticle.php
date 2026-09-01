<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Marketing\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

final class NewsArticle extends Model
{
    use SoftDeletes;

    protected $table = 'real_estate_marketing_news';

    protected $guarded = [];

    protected static function booted(): void
    {
        self::creating(function (self $article): void {
            $article->slug ??= Str::slug($article->title);
        });
    }

    protected function casts(): array
    {
        return ['is_featured' => 'boolean', 'published_at' => 'datetime'];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeVisibleToTeam(Builder $query, int|string|null $teamId): Builder
    {
        return $query->where(fn (Builder $query): Builder => $query->whereNull('team_id')->orWhere('team_id', $teamId));
    }
}
