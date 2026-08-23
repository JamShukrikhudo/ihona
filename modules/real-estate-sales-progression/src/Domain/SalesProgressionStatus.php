<?php

declare(strict_types=1);

namespace Liberu\RealEstate\SalesProgression\Domain;

enum SalesProgressionStatus: string
{
    case InProgress = 'in_progress';
    case OnHold = 'on_hold';
    case Exchanged = 'exchanged';
    case Completed = 'completed';
    case FallenThrough = 'fallen_through';
}
