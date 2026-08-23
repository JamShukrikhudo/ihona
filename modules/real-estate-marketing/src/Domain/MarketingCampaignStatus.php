<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Marketing\Domain;

enum MarketingCampaignStatus: string
{
    case Draft = 'draft';
    case Scheduled = 'scheduled';
    case Active = 'active';
    case Paused = 'paused';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
