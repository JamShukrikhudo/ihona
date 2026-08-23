<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PortalsReporting\Domain;

enum PortalReportStatus: string
{
    case Draft = 'draft';
    case Queued = 'queued';
    case Published = 'published';
    case Failed = 'failed';
    case Expired = 'expired';
    case Archived = 'archived';
}
