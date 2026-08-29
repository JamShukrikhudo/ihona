<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MediaAndDocuments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class MediaDocument extends Model
{
    use SoftDeletes;

    protected $table = 'real_estate_media_documents';

    protected $guarded = ['id'];

    public const GALLERY_KINDS = ['photo' => 'photograph', 'floorplan' => 'floor plan', 'siteplan' => 'site plan'];

    protected function casts(): array
    {
        return ['rights' => 'array', 'metadata' => 'array', 'retention_until' => 'date'];
    }

    public function scopeForTeam($query, int|string $teamId)
    {
        return $query->where('team_id', $teamId);
    }

    public function galleryKind(): ?string
    {
        return self::GALLERY_KINDS[$this->kind] ?? null;
    }

    public function publicUrl(): ?string
    {
        $explicit = data_get($this->metadata, 'public_url');

        return is_string($explicit) && filter_var($explicit, FILTER_VALIDATE_URL) ? $explicit : null;
    }
}
