<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTeam;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfferEvent extends Model
{
    use BelongsToTeam;

    protected $fillable = [
        'team_id',
        'offer_id',
        'actor_id',
        'event_type',
        'previous_amount',
        'amount',
        'previous_status',
        'status',
        'conditions',
        'note',
        'changes',
        'occurred_at',
    ];

    protected function casts(): array
    {
        return [
            'previous_amount' => 'decimal:2',
            'amount' => 'decimal:2',
            'changes' => 'array',
            'occurred_at' => 'datetime',
        ];
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
