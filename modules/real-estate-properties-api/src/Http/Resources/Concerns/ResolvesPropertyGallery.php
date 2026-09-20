<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesApi\Http\Resources\Concerns;

use Illuminate\Support\Facades\Schema;
use Liberu\RealEstate\MediaAndDocuments\Models\MediaDocument;
use Liberu\RealEstate\Properties\Models\Property;

/**
 * Both PropertyResource and PublicPropertyResource used to call
 * Property::galleryItems() with no arguments — that method only ever turns
 * ITS OWN argument into gallery items, so every API response's `gallery`
 * was empty (or, at most, a floor-plan fallback) regardless of how many
 * real photos a property actually had. Mirrors
 * PropertyDetail::mediaItems() (real-estate-properties-livewire), the one
 * place this was already done correctly.
 *
 * Photos now live in Property's own spatie/laravel-medialibrary 'photos'
 * collection; MediaDocument (real-estate-media-and-documents) stays the
 * source for floor plans/site plans — those are still documents, not
 * photography.
 */
trait ResolvesPropertyGallery
{
    /** @return array<int, array{url?: string|null, kind?: string|null, caption?: string|null, staged?: bool}> */
    private function propertyMediaItems(Property $property): array
    {
        $items = $property->photoMediaItems();

        if (Schema::hasTable('real_estate_media_documents')) {
            $items = array_merge($items, MediaDocument::query()
                ->forTeam($property->team_id)
                ->where('property_id', $property->getKey())
                ->whereIn('kind', ['floorplan', 'siteplan'])
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
                ->map(fn (MediaDocument $document): array => [
                    'url' => $document->publicUrl(),
                    'kind' => $document->galleryKind(),
                    'caption' => $document->title,
                    'staged' => (bool) data_get($document->metadata, 'staged', false),
                ])->all());
        }

        return $items;
    }
}
