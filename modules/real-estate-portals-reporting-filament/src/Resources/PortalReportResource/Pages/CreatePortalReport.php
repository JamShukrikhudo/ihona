<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PortalsReportingFilament\Resources\PortalReportResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\PortalsReporting\Application\CreatePortalReport as CreatePortalReportAction;
use Liberu\RealEstate\PortalsReportingFilament\Resources\PortalReportResource;

final class CreatePortalReport extends CreateRecord
{
    protected static string $resource = PortalReportResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = auth()->user();
        abort_unless($user?->current_team_id !== null, 403);

        return app(CreatePortalReportAction::class)->handle($user->current_team_id, $user->getAuthIdentifier(), $data);
    }
}
