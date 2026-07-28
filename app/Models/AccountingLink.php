<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingLink extends Model
{
    use BelongsToTeam, HasFactory;

    protected $fillable = [
        'team_id', 'accounting_integration_id', 'link_type', 'linkable_type',
        'linkable_id', 'external_id', 'invoice_reference', 'payment_status',
        'amount', 'currency', 'due_date', 'last_synced_at', 'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'last_synced_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function linkable()
    {
        return $this->morphTo();
    }
}
