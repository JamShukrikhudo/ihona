<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Properties\Domain;

final readonly class PropertyGalleryItem
{
    public function __construct(
        public string $url,
        public string $kind,
        public ?string $caption = null,
        public bool $staged = false,
    ) {}

    public function alt(): string
    {
        return $this->caption ?? ucfirst($this->kind);
    }

    public function isPlan(): bool
    {
        return $this->kind !== 'photograph';
    }

    /** @return array{url: string, kind: string, caption: ?string, staged: bool} */
    public function toArray(): array
    {
        return ['url' => $this->url, 'kind' => $this->kind, 'caption' => $this->caption, 'staged' => $this->staged];
    }
}
