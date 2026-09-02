<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MediaAndDocuments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Liberu\Foundation\Organizations\Models\Team;

final class MediaDocument extends Model
{
    use SoftDeletes;

    protected $table = 'real_estate_media_documents';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['rights' => 'array', 'metadata' => 'array', 'allowed_user_ids' => 'array', 'allowed_roles' => 'array', 'is_signable' => 'boolean', 'retention_until' => 'date'];
    }

    public function versions(): HasMany
    {
        return $this->hasMany(DocumentVersion::class, 'media_document_id')->latest('version');
    }

    public function signatures(): HasMany
    {
        return $this->hasMany(DocumentSignature::class, 'media_document_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(DocumentCategory::class, 'real_estate_document_category_media');
    }

    public function scopeForTeam($query, int|string $teamId)
    {
        return $query->where('team_id', $teamId);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
