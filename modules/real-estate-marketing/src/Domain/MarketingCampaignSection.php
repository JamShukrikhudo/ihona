<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Marketing\Domain;

enum MarketingCampaignSection: string
{
    case Audience = 'audience';
    case Content = 'content';
    case Schedule = 'schedule';
    case Metrics = 'metrics';
}
