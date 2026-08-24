<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PortalsReportingFilament\Resources\PortalReportResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\PortalsReporting\Application\UpdatePortalReport;
use Liberu\RealEstate\PortalsReportingFilament\Resources\PortalReportResource;

final class EditPortalReport extends EditRecord
{
    protected static string $resource = PortalReportResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null && (string) $teamId === (string) $record->team_id, 403);

        return app(UpdatePortalReport::class)->handle($record, $teamId, $data);
    }
}
