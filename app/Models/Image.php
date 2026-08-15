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
     * A public URL, or null when the file lives somewhere the site cannot
     * serve from.
     *
     * This used to return asset('storage/…') for every row regardless of disk.
     * The V1 media API stores to `local`, which is private and has no public
     * path, so every image uploaded through it produced a URL that 404s — a
     * broken image icon where a room should be. A disk with no `url` in its
     * config has no public address, and saying so is better than inventing one.
     */
    public function getUrlAttribute(): ?string
    {
        if (! $this->file_path) {
            return null;
        }

        $disk = $this->disk ?: 'public';

        if (! config("filesystems.disks.{$disk}.url")) {
            return null;
        }

        return Storage::disk($disk)->url($this->file_path);
    }
}
