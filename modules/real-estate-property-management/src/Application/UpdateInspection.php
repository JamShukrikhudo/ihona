<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertyManagement\Application;

use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\PropertyManagement\Domain\InspectionStatus;
use Liberu\RealEstate\PropertyManagement\Models\Inspection;

final class UpdateInspection
{
    public function handle(Inspection $inspection, int|string $teamId, array $attributes): Inspection
    {
        abort_unless((string) $inspection->team_id === (string) $teamId, 404);
        if ($inspection->status === InspectionStatus::Completed && array_key_exists('status', $attributes) && $attributes['status'] !== InspectionStatus::Completed->value) {
            throw ValidationException::withMessages(['status' => 'Completed inspections cannot be reopened.']);
        }
        if (array_key_exists('status', $attributes) && InspectionStatus::tryFrom((string) $attributes['status']) === null) {
            throw ValidationException::withMessages(['status' => 'Select a valid inspection status.']);
        }
        $inspection->forceFill($attributes)->save();

        return $inspection->refresh();
    }
}
