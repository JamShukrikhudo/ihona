<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertyManagement\Domain;

enum VendorQuoteStatus: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case Expired = 'expired';
    case Withdrawn = 'withdrawn';
}
