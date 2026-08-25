<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Lettings\Application;

use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Lettings\Domain\LettingCapability;
use Liberu\RealEstate\Lettings\Domain\LettingStatus;
use Liberu\RealEstate\Lettings\Models\Letting;

final class UpdateLetting
{
    public function __construct(private readonly TransitionLetting $transition) {}

    public function handle(Letting $letting, int|string $teamId, int|string $actorId, array $attributes): Letting
    {
        abort_unless((string) $letting->team_id === (string) $teamId, 404);

        if (array_key_exists('subject', $attributes) && trim((string) $attributes['subject']) === '') {
            throw ValidationException::withMessages(['subject' => 'A letting subject is required.']);
        }
        if (array_key_exists('capability', $attributes) && LettingCapability::tryFrom((string) $attributes['capability']) === null) {
            throw ValidationException::withMessages(['capability' => 'Select a valid letting capability.']);
        }
        $status = null;
        if (array_key_exists('status', $attributes)) {
            $status = LettingStatus::tryFrom((string) $attributes['status']);
            if ($status === null) {
                throw ValidationException::withMessages(['status' => 'Select a valid letting status.']);
            }
            unset($attributes['status']);
        }

        $letting->fill($attributes);
        $letting->audit = [...($letting->audit ?? []), ['event' => 'updated', 'actor_id' => $actorId, 'at' => now()->toISOString()]];
        $letting->save();

        $letting = $letting->refresh();

        return $status !== null && $status !== $letting->status
            ? $this->transition->handle($letting, $teamId, $actorId, $status)
            : $letting;
    }
}
