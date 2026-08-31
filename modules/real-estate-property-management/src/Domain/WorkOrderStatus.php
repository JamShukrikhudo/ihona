<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertyManagement\Domain;

enum WorkOrderStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Scheduled = 'scheduled';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
