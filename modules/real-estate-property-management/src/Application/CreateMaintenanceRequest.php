<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertyManagement\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\PropertyManagement\Domain\MaintenancePriority;
use Liberu\RealEstate\PropertyManagement\Models\MaintenanceRequest;

final class CreateMaintenanceRequest
{
    public function handle(int|string $teamId, int|string $actorId, array $attributes): MaintenanceRequest
    {
        foreach (['property_id', 'title', 'description', 'requested_date'] as $field) {
            if (! filled($attributes[$field] ?? null)) {
                throw ValidationException::withMessages([$field => 'This field is required.']);
            }
        }
        if (MaintenancePriority::tryFrom((string) ($attributes['priority'] ?? MaintenancePriority::Normal->value)) === null) {
            throw ValidationException::withMessages(['priority' => 'Select a valid maintenance priority.']);
        }

        return DB::transaction(fn (): MaintenanceRequest => MaintenanceRequest::query()->create([
            ...$attributes,
            'team_id' => $teamId,
            'created_by' => $attributes['created_by'] ?? $actorId,
        ]));
    }
}
