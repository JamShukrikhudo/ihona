<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class CalendarEntry extends Model
{
    use SoftDeletes;

    protected $table = 'real_estate_calendar_entries';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['starts_at' => 'datetime', 'ends_at' => 'datetime', 'reminder_at' => 'datetime', 'all_day' => 'boolean', 'attendee_user_ids' => 'array', 'contact_ids' => 'array', 'recurrence' => 'array'];
    }

    public function scopeForTeam(Builder $query, int|string $teamId): Builder
    {
        return $query->where('team_id', $teamId);
    }
}
