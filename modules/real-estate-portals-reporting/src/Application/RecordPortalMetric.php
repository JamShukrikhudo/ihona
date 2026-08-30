<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PortalsReporting\Application;

use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\PortalsReporting\Domain\PortalMetric;
use Liberu\RealEstate\PortalsReporting\Models\PortalReport;

final class RecordPortalMetric
{
    public function handle(PortalReport $report, int|string $teamId, PortalMetric $metric, float|int|string $value): PortalReport
    {
        if ((string) $report->team_id !== (string) $teamId) {
            throw ValidationException::withMessages(['report' => 'The report does not belong to this team.']);
        }
        if (! is_numeric($value)) {
            throw ValidationException::withMessages(['value' => 'A numeric metric value is required.']);
        }

        $metrics = $report->metrics ?? [];
        $metrics[$metric->value] = (float) $value;
        $report->forceFill(['metrics' => $metrics])->save();

        return $report->refresh();
    }
}
