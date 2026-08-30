<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Viewings\Queries;

use Carbon\CarbonImmutable;
use Liberu\RealEstate\Viewings\Domain\ViewingStatus;
use Liberu\RealEstate\Viewings\Models\Viewing;

final class AvailableViewingSlots
{
    /** @return list<string> ISO-8601 start timestamps in the application's timezone */
    public function handle(
        int|string $teamId,
        int|string|null $propertyId,
        CarbonImmutable $date,
        int $durationMinutes = 60,
    ): array {
        if ($date->isPast() || $date->isWeekend() || $durationMinutes < 15 || $durationMinutes > 240) {
            return [];
        }

        $day = $date->startOfDay();
        $opening = $day->setTime(9, 0);
        $closing = $day->setTime(17, 0);
        $latestStart = $closing->subMinutes($durationMinutes);
        $occupied = Viewing::query()
            ->forTeam($teamId)
            ->forProperty($propertyId)
            ->whereIn('status', [ViewingStatus::Requested, ViewingStatus::Confirmed])
            ->whereDate('starts_at', $day)
            ->get(['starts_at', 'ends_at']);

        $slots = [];
        for ($slot = $opening; $slot->lte($latestStart); $slot = $slot->addHour()) {
            $slotEnd = $slot->addMinutes($durationMinutes);
            $conflicts = $occupied->contains(
                fn (Viewing $viewing): bool => $slot->lt($viewing->ends_at ?? CarbonImmutable::parse($viewing->starts_at)->addHour())
                    && $slotEnd->gt($viewing->starts_at),
            );

            if (! $conflicts) {
                $slots[] = $slot->toIso8601String();
            }
        }

        return $slots;
    }
}
