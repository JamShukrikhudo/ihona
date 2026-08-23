<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Rightmove\Domain;

enum RightmoveSyncStatus: string
{
    case Pending = 'pending';
    case Syncing = 'syncing';
    case Synced = 'synced';
    case Failed = 'failed';
    case Disabled = 'disabled';
}
