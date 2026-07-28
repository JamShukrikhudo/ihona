<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use BelongsToTeam, HasFactory, SoftDeletes;

    protected $fillable = [
        'team_id', 'branch_id', 'company_id', 'type', 'title', 'first_name',
        'last_name', 'emails', 'phones', 'addresses', 'tags', 'notes',
        'preferred_language', 'status', 'last_contacted_at',
    ];

    protected function casts(): array
    {
        return [
            'emails' => 'array',
            'phones' => 'array',
            'addresses' => 'array',
            'tags' => 'array',
            'last_contacted_at' => 'datetime',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }

    public function communications(): MorphMany
    {
        return $this->morphMany(Communication::class, 'communicable');
    }

    public function outgoingRelationships(): HasMany
    {
        return $this->hasMany(ContactRelationship::class);
    }

    public function incomingRelationships(): HasMany
    {
        return $this->hasMany(ContactRelationship::class, 'related_contact_id');
    }
}
