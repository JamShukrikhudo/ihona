<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Properties\Domain;

enum PriceAlertFrequency: string
{
    case Daily = 'daily';
    case Weekly = 'weekly';
    case Monthly = 'monthly';
}
