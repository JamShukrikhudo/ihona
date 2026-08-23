<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PortalsReportingFilament\Resources\PortalReportResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\RealEstate\PortalsReportingFilament\Resources\PortalReportResource;

final class CreatePortalReport extends CreateRecord
{
    protected static string $resource = PortalReportResource::class;
}
