<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Offers\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Liberu\RealEstate\Offers\Domain\OfferStatus;

final class Offer extends Model
{
    use SoftDeletes;

    protected $table = 'real_estate_offers';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['status' => OfferStatus::class, 'terms' => 'array', 'qualification' => 'array', 'negotiation' => 'array', 'proof' => 'array', 'decision_history' => 'array', 'accepted_controls' => 'array', 'amount' => 'decimal:2'];
    }

    public function scopeForTeam($query, int|string $teamId)
    {
        return $query->where('team_id', $teamId);
    }
}
