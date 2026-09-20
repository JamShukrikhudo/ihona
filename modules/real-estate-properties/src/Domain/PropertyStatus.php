<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Properties\Domain;

/**
 * Publication status only — not the deal outcome. A published listing that
 * gets sold/let/reserved is a fact about a Deal/Offer, not about whether
 * the listing itself is visible; keeping them separate means an accepted
 * offer doesn't silently hide the listing, and a withdrawn listing doesn't
 * erase its deal history.
 */
enum PropertyStatus: string
{
    case Draft = 'draft';
    case Moderation = 'moderation';
    case Published = 'published';
    case Archive = 'archive';

    public function isPublic(): bool
    {
        return $this === self::Published;
    }
}
