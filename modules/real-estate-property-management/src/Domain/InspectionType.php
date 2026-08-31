<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertyManagement\Domain;

enum InspectionType: string
{
    case Routine = 'routine';
    case CheckIn = 'check_in';
    case CheckOut = 'check_out';
    case MidTenancy = 'mid_tenancy';
}
