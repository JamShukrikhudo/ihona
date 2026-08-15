<?php

namespace App\Support;

/**
 * One tile in a property gallery, whichever store it came from.
 *
 * The page has two: media-library rows written by the admin panel, and the
 * `images` table written by the V1 media API and the staging service. Only the
 * second carries a caption, a type and a staged flag, so the view would have
 * had to know which store it was reading from to render a tile. It reads this
 * instead.
 */
readonly class GalleryItem
{
    public function __construct(
        public string $url,
        public ?string $caption = null,
        /** photograph | floor plan | site plan */
        public string $kind = 'photograph',
        public bool $staged = false,
        public ?string $stagingStyle = null,
    ) {}

    /**
     * What a screen reader is told. A caption is the room's name, so it is the
     * best description available; failing that the kind at least says whether
     * this is a room or a drawing.
     */
    public function alt(): string
    {
        return $this->caption ?? ucfirst($this->kind);
    }

    public function isPlan(): bool
    {
        return $this->kind !== 'photograph';
    }
}
