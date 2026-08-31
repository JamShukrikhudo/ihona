<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertyManagement\Domain;

enum MaintenancePriority: string
{
    case Low = 'low';
    case Normal = 'normal';
    case High = 'high';
    case Urgent = 'urgent';
}
