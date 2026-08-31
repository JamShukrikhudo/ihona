<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Lettings\Domain;

enum RentalChargeStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Overdue = 'overdue';
}
