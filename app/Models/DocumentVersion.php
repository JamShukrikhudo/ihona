<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentVersion extends Model
{
    protected $fillable = [
        'team_id', 'document_id', 'uploaded_by', 'version', 'file_name',
        'file_path', 'mime_type', 'size', 'checksum', 'notes',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function signatures(): HasMany
    {
        return $this->hasMany(DigitalSignature::class);
    }
}
