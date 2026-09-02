<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertyManagement\Domain;

enum InspectionStatus: string
{
    case Scheduled = 'scheduled';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
