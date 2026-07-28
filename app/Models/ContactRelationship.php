<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTeam;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactRelationship extends Model
{
    use BelongsToTeam;

    protected $fillable = [
        'team_id',
        'contact_id',
        'related_contact_id',
        'relationship',
        'inverse_relationship',
        'notes',
        'created_by',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function relatedContact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'related_contact_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
