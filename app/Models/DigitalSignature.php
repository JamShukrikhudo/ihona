<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DigitalSignature extends Model
{
    protected $table = 'digital_signatures';

    protected $fillable = [
        'user_id',
        'team_id',
        'document_id',
        'document_version_id',
        'signature_data',
        'signed_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function version()
    {
        return $this->belongsTo(DocumentVersion::class, 'document_version_id');
    }
}
