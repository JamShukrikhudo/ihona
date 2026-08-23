<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Properties\Domain;

enum PropertyStatus: string
{
    case Draft = 'draft';
    case Available = 'available';
    case UnderOffer = 'under_offer';
    case Sold = 'sold';
    case Let = 'let';
    case Withdrawn = 'withdrawn';

    public function isPublic(): bool
    {
        return in_array($this, [self::Available, self::UnderOffer], true);
    }
}
