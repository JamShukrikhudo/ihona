<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertyManagement\Application;

use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\PropertyManagement\Domain\MaintenanceStatus;
use Liberu\RealEstate\PropertyManagement\Models\MaintenanceRequest;

final class UpdateMaintenanceRequest
{
    public function handle(MaintenanceRequest $request, int|string $teamId, array $attributes): MaintenanceRequest
    {
        abort_unless((string) $request->team_id === (string) $teamId, 404);
        if ($request->status === MaintenanceStatus::Completed && ($attributes['status'] ?? null) !== MaintenanceStatus::Completed->value && array_key_exists('status', $attributes)) {
            throw ValidationException::withMessages(['status' => 'Completed maintenance requests cannot be reopened.']);
        }
        if (array_key_exists('status', $attributes) && MaintenanceStatus::tryFrom((string) $attributes['status']) === null) {
            throw ValidationException::withMessages(['status' => 'Select a valid maintenance status.']);
        }
        $request->forceFill($attributes)->save();

        return $request->refresh();
    }
}
