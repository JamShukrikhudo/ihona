<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Viewings\Domain;

enum ViewingStatus: string
{
    case Requested = 'requested';
    case Confirmed = 'confirmed';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case NoShow = 'no_show';
}
