<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Listings\Domain;

enum ListingSection: string
{
    case ChannelContent = 'channel_content';
    case PublicationRules = 'publication_rules';
    case PortalFeeds = 'portal_feeds';
    case Reconciliation = 'reconciliation';
}
