<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    use HasFactory;

    protected $primaryKey = 'image_id';

    protected $fillable = [
        'team_id',
        'property_id',
        'type',
        'title',
        'alt_text',
        'is_staged',
        'original_image_id',
        'staging_style',
        'staging_metadata',
        'staging_provider',
        'file_path',
        'file_name',
        'mime_type',
        'disk',
        'file_size',
        'sort_order',
        'is_primary',
        'is_public',
        'watermark',
        'metadata',
    ];

    protected $casts = [
        'is_staged' => 'boolean',
        'staging_metadata' => 'array',
        'is_primary' => 'boolean',
        'is_public' => 'boolean',
        'watermark' => 'boolean',
        'metadata' => 'array',
        'file_size' => 'integer',
        'sort_order' => 'integer',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function originalImage(): BelongsTo
    {
        return $this->belongsTo(Image::class, 'original_image_id', 'image_id');
    }

    public function stagedVersions(): HasMany
    {
        return $this->hasMany(Image::class, 'original_image_id', 'image_id');
    }

    public function isStaged(): bool
    {
        return $this->is_staged;
    }

    public function hasStagedVersions(): bool
    {
        return $this->stagedVersions()->exists();
    }

    /**
     * A URL the browser can fetch, or null when there is nothing to fetch.
     *
     * This used to return asset('storage/…') for every row regardless of disk.
     * The V1 media API stores to `local` — which is also the column default —
     * and `local` is private with no public path, so every image the app itself
     * writes produced a URL that 404s.
     *
     * A disk with a public URL is addressed directly. Anything else goes
     * through the route, which checks `is_public` before it serves a byte.
     */
    public function getUrlAttribute(): ?string
    {
        if (blank($this->file_path)) {
            return null;
        }

        $disk = $this->disk ?: 'public';

        if (config("filesystems.disks.{$disk}.url")) {
            return Storage::disk($disk)->url($this->file_path);
        }

        if (! $this->is_public || blank($this->image_id) || blank($this->property_id)) {
            return null;
        }

        return route('property.media', [
            'property' => $this->property_id,
            'medium' => $this->image_id,
        ]);
    }
}
