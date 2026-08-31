<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PortalsReporting\Application;

use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\PortalsReporting\Domain\PortalReportStatus;
use Liberu\RealEstate\PortalsReporting\Models\PortalReport;

final class TransitionPortalReport
{
    public function handle(PortalReport $report, int|string $teamId, PortalReportStatus $status): PortalReport
    {
        if ((string) $report->team_id !== (string) $teamId) {
            throw ValidationException::withMessages(['report' => 'The report does not belong to this team.']);
        }

        $current = $report->status;
        $allowed = match ($current) {
            PortalReportStatus::Draft => [PortalReportStatus::Queued, PortalReportStatus::Archived],
            PortalReportStatus::Queued => [PortalReportStatus::Published, PortalReportStatus::Failed],
            PortalReportStatus::Published => [PortalReportStatus::Expired, PortalReportStatus::Archived],
            PortalReportStatus::Failed => [PortalReportStatus::Queued, PortalReportStatus::Archived],
            PortalReportStatus::Expired, PortalReportStatus::Archived => [],
        };

        if (! in_array($status, $allowed, true)) {
            throw ValidationException::withMessages(['status' => "Cannot transition a {$current->value} report to {$status->value}."]);
        }

        $attributes = ['status' => $status];
        if ($status === PortalReportStatus::Published) {
            $attributes['published_at'] = now();
        }
        $report->forceFill($attributes)->save();

        return $report->refresh();
    }
}
