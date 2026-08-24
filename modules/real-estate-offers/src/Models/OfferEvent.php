<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Offers\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class OfferEvent extends Model
{
    protected $table = 'real_estate_offer_events';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['previous_amount' => 'decimal:2', 'amount' => 'decimal:2', 'changes' => 'array', 'occurred_at' => 'datetime'];
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }
}
