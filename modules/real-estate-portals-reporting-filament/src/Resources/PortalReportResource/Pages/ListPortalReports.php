<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PortalsReportingFilament\Resources\PortalReportResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\RealEstate\PortalsReportingFilament\Resources\PortalReportResource;

final class ListPortalReports extends ListRecords
{
    protected static string $resource = PortalReportResource::class;
}
