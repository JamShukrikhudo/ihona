<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PortalsReporting\Domain;

enum PortalMetric: string
{
    case Conversion = 'conversion';
    case Source = 'source';
    case Fee = 'fee';
    case TimeToComplete = 'time_to_complete';
    case Occupancy = 'occupancy';
    case Compliance = 'compliance';
}
