<?php

namespace App\Models\Concerns;

use App\Models\Image;
use App\Support\GalleryItem;
use Illuminate\Support\Collection;

/**
 * Everything the detail page can show a picture of, in the order a buyer wants
 * it: the rooms first, then the drawings of the building they are in.
 *
 * The floor plan used to sit 900 lines below the photographs in its own
 * widget, which is the one thing a buyer scrolls back up looking for.
 */
trait HasGallery
{
    /**
     * Media types that are pictures of the property. `epc`, `brochure` and
     * `document` are paperwork — they belong in the disclosure panel, not in a
     * carousel of rooms.
     */
    private const GALLERY_KINDS = [
        'image' => 'photograph',
        'floorplan' => 'floor plan',
        'siteplan' => 'site plan',
    ];

    private const KIND_ORDER = ['photograph' => 0, 'floor plan' => 1, 'site plan' => 2];

    /**
     * @return Collection<int, GalleryItem>
     */
    public function gallery(): Collection
    {
        $items = $this->galleryFromMediaTable()
            ->concat($this->galleryFromMediaLibrary());

        if ($items->doesntContain(fn (GalleryItem $item) => $item->kind === 'floor plan')) {
            $items = $items->concat($this->galleryFromFloorPlanColumn());
        }

        // A stable sort: photographs, then plans, each group keeping the order
        // the agency put them in.
        return $items
            ->sortBy(fn (GalleryItem $item) => self::KIND_ORDER[$item->kind], SORT_REGULAR, false)
            ->values();
    }

    /**
     * @return Collection<int, GalleryItem>
     */
    private function galleryFromMediaTable(): Collection
    {
        return $this->images
            ->filter(fn (Image $image) => $image->is_public !== false)
            ->filter(fn (Image $image) => isset(self::GALLERY_KINDS[$image->type]))
            // The V1 media API stores to the private `local` disk, which has no
            // public URL at all; rendering one gives a broken image icon where
            // a room should be.
            ->filter(fn (Image $image) => filled($image->url))
            ->sortBy([
                fn (Image $a, Image $b) => ($b->is_primary ? 1 : 0) <=> ($a->is_primary ? 1 : 0),
                fn (Image $a, Image $b) => ($a->sort_order ?? 0) <=> ($b->sort_order ?? 0),
                fn (Image $a, Image $b) => $a->image_id <=> $b->image_id,
            ])
            ->map(fn (Image $image) => new GalleryItem(
                url: $image->url,
                // Never the filename: "DSC_4417.jpg" tells a buyer nothing and
                // reads as an oversight. No name given means no caption.
                caption: $image->title ?: ($image->alt_text ?: null),
                kind: self::GALLERY_KINDS[$image->type],
                staged: (bool) $image->is_staged,
                stagingStyle: $image->staging_style,
            ))
            ->values();
    }

    /**
     * @return Collection<int, GalleryItem>
     */
    private function galleryFromMediaLibrary(): Collection
    {
        return $this->getMedia('images')
            ->map(fn ($media) => new GalleryItem(
                url: $media->getUrl(),
                caption: $media->getCustomProperty('caption') ?: null,
            ))
            ->pipe(fn ($media) => collect($media->all()));
    }

    /**
     * The oldest of the three stores: a single path in a column, from before
     * either media table existed. Only consulted when nothing newer holds one.
     *
     * @return Collection<int, GalleryItem>
     */
    private function galleryFromFloorPlanColumn(): Collection
    {
        $path = $this->floor_plan_image ?: data_get($this->floor_plan_data, 'image');

        if (blank($path)) {
            return collect();
        }

        return collect([new GalleryItem(
            url: str_starts_with((string) $path, 'http') ? $path : asset('storage/'.ltrim((string) $path, '/')),
            kind: 'floor plan',
        )]);
    }
}
