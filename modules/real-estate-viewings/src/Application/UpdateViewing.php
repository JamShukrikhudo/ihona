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
