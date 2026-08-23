<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PortalsReporting\Application;

use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\PortalsReporting\Models\PortalReport;

final class UpdatePortalReport
{
    public function handle(PortalReport $report, int|string $teamId, array $attributes): PortalReport
    {
        if ((string) $report->team_id !== (string) $teamId) {
            throw ValidationException::withMessages(['report' => 'The report does not belong to this team.']);
        }$data = $attributes;
        if (array_key_exists('portal', $data) && trim((string) $data['portal']) === '') {
            throw ValidationException::withMessages(['portal' => 'A portal name is required.']);
        }$report->fill($data)->save();

        return $report->refresh();
    }
}
