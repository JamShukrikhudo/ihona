<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertyManagement\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\PropertyManagement\Domain\InspectionStatus;
use Liberu\RealEstate\PropertyManagement\Domain\InspectionType;
use Liberu\RealEstate\PropertyManagement\Models\Inspection;

final class CreateInspection
{
    public function handle(int|string $teamId, int|string $actorId, array $attributes): Inspection
    {
        if (! isset($attributes['property_id'])) {
            throw ValidationException::withMessages(['property_id' => 'A property is required.']);
        }
        $type = InspectionType::tryFrom((string) ($attributes['type'] ?? ''));
        if ($type === null) {
            throw ValidationException::withMessages(['type' => 'Select a valid inspection type.']);
        }
        if (! isset($attributes['scheduled_at'])) {
            throw ValidationException::withMessages(['scheduled_at' => 'A scheduled time is required.']);
        }

        return DB::transaction(fn (): Inspection => Inspection::query()->create([
            ...$attributes,
            'team_id' => $teamId,
            'created_by' => $attributes['created_by'] ?? $actorId,
            'type' => $type,
            'status' => InspectionStatus::Scheduled,
        ]));
    }
}
