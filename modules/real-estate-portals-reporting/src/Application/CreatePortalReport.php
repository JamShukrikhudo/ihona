<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PortalsReporting\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\PortalsReporting\Domain\PortalReportStatus;
use Liberu\RealEstate\PortalsReporting\Models\PortalReport;

final class CreatePortalReport
{
    public function handle(int|string $teamId, int|string $actorId, array $attributes): PortalReport
    {
        $portal = trim((string) ($attributes['portal'] ?? ''));
        $type = trim((string) ($attributes['report_type'] ?? ''));
        if ($portal === '') {
            throw ValidationException::withMessages(['portal' => 'A portal name is required.']);
        }if ($type === '') {
            throw ValidationException::withMessages(['report_type' => 'A report type is required.']);
        }

        return DB::transaction(fn (): PortalReport => PortalReport::query()->create(['team_id' => $teamId, 'created_by' => $actorId, 'property_id' => $attributes['property_id'] ?? null, 'listing_id' => $attributes['listing_id'] ?? null, 'portal' => $portal, 'report_type' => $type, 'status' => $attributes['status'] ?? PortalReportStatus::Draft, 'payload' => $attributes['payload'] ?? [], 'metrics' => $attributes['metrics'] ?? [], 'published_at' => $attributes['published_at'] ?? null, 'generated_at' => $attributes['generated_at'] ?? null, 'expires_at' => $attributes['expires_at'] ?? null, 'error' => $attributes['error'] ?? null]));
    }
}
