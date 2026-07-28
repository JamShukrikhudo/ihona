<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Offer extends Model
{
    use BelongsToTeam, HasFactory, SoftDeletes;

    protected $fillable = [
        'team_id', 'property_id', 'contact_id', 'negotiator_id', 'amount',
        'currency', 'status', 'mortgage_status', 'chain_information',
        'conditions', 'offered_at', 'responded_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'offered_at' => 'datetime',
            'responded_at' => 'datetime',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function negotiator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'negotiator_id');
    }
}
