<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Liberu\RealEstate\MediaAndDocuments\Models\MediaDocument;
use Liberu\RealEstate\Properties\Models\Property;

/**
 * One-time backfill: copies existing kind='photo' MediaDocument rows into
 * Property's new spatie/laravel-medialibrary 'photos' collection (see
 * ResolvesPropertyGallery / Property::photoMediaItems()). Confirmed via
 * tinker before writing this that the local dev database has zero such
 * rows, so this is written for whatever real photos exist on production —
 * verify the result manually there (compare MediaDocument::where('kind',
 * 'photo')->count() against Property::get()->sum(fn ($p) =>
 * $p->getMedia('photos')->count())) rather than trusting this migration
 * blindly. A single row's failure (network hiccup, missing file, taken-
 * down URL) is logged and skipped rather than aborting the whole run —
 * this is a best-effort copy, not a schema change.
 */
return new class() extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('real_estate_media_documents')) {
            return;
        }

        MediaDocument::query()
            ->where('kind', 'photo')
            ->whereNotNull('property_id')
            ->orderBy('property_id')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->each(function (MediaDocument $document): void {
                $url = $document->publicUrl();
                if ($url === null) {
                    return;
                }

                $property = Property::query()->find($document->property_id);
                if ($property === null) {
                    return;
                }

                try {
                    $property->addMediaFromUrl($url)
                        ->usingName($document->title ?? $document->file_name ?? 'photo')
                        ->withCustomProperties(['staged' => (bool) data_get($document->metadata, 'staged', false)])
                        ->toMediaCollection('photos');
                } catch (Throwable $exception) {
                    Log::warning('Skipped migrating a photo MediaDocument to the medialibrary.', [
                        'media_document_id' => $document->getKey(),
                        'property_id' => $document->property_id,
                        'error' => $exception->getMessage(),
                    ]);
                }
            });
    }

    public function down(): void
    {
        // Intentionally irreversible: MediaDocument rows are untouched by
        // up(), so there's nothing here to undo beyond the medialibrary
        // rows themselves, which the media table's own migration drops.
    }
};
