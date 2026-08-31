<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertyManagement\Application;

use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\PropertyManagement\Domain\ManagementCapability;
use Liberu\RealEstate\PropertyManagement\Domain\ManagementStatus;
use Liberu\RealEstate\PropertyManagement\Models\ManagementRecord;

final class UpdateManagementRecord
{
    public function __construct(private readonly TransitionManagementRecord $transition) {}

    public function handle(ManagementRecord $record, int|string $teamId, int|string $actorId, array $attributes): ManagementRecord
    {
        abort_unless((string) $record->team_id === (string) $teamId, 404);

        if (array_key_exists('subject', $attributes) && trim((string) $attributes['subject']) === '') {
            throw ValidationException::withMessages(['subject' => 'A management subject is required.']);
        }
        if (array_key_exists('capability', $attributes) && ManagementCapability::tryFrom((string) $attributes['capability']) === null) {
            throw ValidationException::withMessages(['capability' => 'Select a valid management capability.']);
        }
        $status = null;
        if (array_key_exists('status', $attributes)) {
            $status = ManagementStatus::tryFrom((string) $attributes['status']);
            if ($status === null) {
                throw ValidationException::withMessages(['status' => 'Select a valid management status.']);
            }
            unset($attributes['status']);
        }

        $record->fill($attributes);
        $record->audit = [...($record->audit ?? []), ['event' => 'updated', 'actor_id' => $actorId, 'at' => now()->toISOString()]];
        $record->save();

        $record = $record->refresh();

        return $status !== null && $status !== $record->status
            ? $this->transition->handle($record, $teamId, $actorId, $status)
            : $record;
    }
}
