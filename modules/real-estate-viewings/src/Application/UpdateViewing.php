<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Viewings\Application;

use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Viewings\Domain\ViewingStatus;
use Liberu\RealEstate\Viewings\Models\Viewing;

final class UpdateViewing
{
    public function handle(Viewing $viewing, int|string $teamId, array $attributes): Viewing
    {
        abort_unless((string) $viewing->team_id === (string) $teamId, 404);
        if (array_key_exists('subject', $attributes) && trim((string) $attributes['subject']) === '') {
            throw ValidationException::withMessages(['subject' => 'A viewing subject is required.']);
        }
        if (array_key_exists('starts_at', $attributes) && now()->gte($attributes['starts_at'])) {
            throw ValidationException::withMessages(['starts_at' => 'A viewing must start in the future.']);
        }
        if (array_key_exists('property_id', $attributes) && (string) $attributes['property_id'] !== (string) $viewing->property_id) {
            throw ValidationException::withMessages(['property_id' => 'A viewing cannot be moved to another property.']);
        }
        if (array_key_exists('ends_at', $attributes) && filled($attributes['ends_at']) && $attributes['ends_at'] <= $attributes['starts_at']) {
            throw ValidationException::withMessages(['ends_at' => 'The end time must be after the start time.']);
        }
        if (array_key_exists('starts_at', $attributes) && ! $viewing->canBeRescheduled()) {
            throw ValidationException::withMessages(['starts_at' => 'This viewing cannot be rescheduled.']);
        }
        if (array_key_exists('status', $attributes)) {
            $status = ViewingStatus::tryFrom((string) $attributes['status']);
            if ($status === null || ! $viewing->canTransitionTo($status)) {
                throw ValidationException::withMessages(['status' => 'That viewing status transition is not allowed.']);
            }
        }
        $viewing->fill($attributes);
        $viewing->save();

        return $viewing->fresh();
    }
}
