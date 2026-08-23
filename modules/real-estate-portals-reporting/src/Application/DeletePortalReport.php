<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PortalsReporting\Application;

use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\PortalsReporting\Models\PortalReport;

final class DeletePortalReport
{
    public function handle(PortalReport $report, int|string $teamId): void
    {
        if ((string) $report->team_id !== (string) $teamId) {
            throw ValidationException::withMessages(['report' => 'The report does not belong to this team.']);
        }$report->delete();
    }
}
