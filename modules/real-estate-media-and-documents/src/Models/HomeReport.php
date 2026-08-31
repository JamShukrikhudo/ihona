<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MediaAndDocuments\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class HomeReport extends Model
{
    use SoftDeletes;

    protected $table = 'real_estate_home_reports';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['survey_date' => 'date', 'expiry_date' => 'date', 'condition_categories' => 'array'];
    }

    public function scopeForTeam(Builder $q, int|string $id): Builder
    {
        return $q->where('team_id', $id);
    }

    public function scopeExpired(Builder $q): Builder
    {
        return $q->whereNotNull('expiry_date')->where('expiry_date', '<', now()->toDateString());
    }

    public function scopeValid(Builder $q): Builder
    {
        return $q->where(fn ($q) => $q->whereNull('expiry_date')->orWhere('expiry_date', '>=', now()->toDateString()));
    }

    public function isExpired(): bool
    {
        return $this->expiry_date?->isPast() ?? false;
    }

    public function isValid(): bool
    {
        return ! $this->isExpired() && in_array($this->energy_band, [null, 'A', 'B', 'C', 'D', 'E', 'F', 'G'], true);
    }

    public function overallCondition(): string
    {
        $ratings = [(int) $this->property_condition, ...array_map('intval', array_values(is_array($this->condition_categories) ? $this->condition_categories : []))];

        return (string) max($ratings);
    }

    public function conditionLabel(): string
    {
        return ['1' => 'No action required', '2' => 'Minor action required', '3' => 'Action required', '4' => 'Urgent action required'][$this->overallCondition()] ?? 'Action required';
    }

    public function energyImprovementPoints(): int
    {
        return max(0, (int) $this->energy_potential_score - (int) $this->energy_current_score);
    }
}
