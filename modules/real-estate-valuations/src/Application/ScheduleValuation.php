<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Valuations\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Valuations\Domain\ValuationStatus;
use Liberu\RealEstate\Valuations\Models\Valuation;

final class ScheduleValuation
{
    public function handle(Valuation $valuation, int|string $teamId, string $scheduledAt): Valuation
    {
        abort_unless((string) $valuation->team_id === (string) $teamId, 404);
        if (! $valuation->canTransitionTo(ValuationStatus::Scheduled)) {
            throw ValidationException::withMessages(['status' => 'Only draft valuations can be scheduled.']);
        }

        return DB::transaction(function () use ($valuation, $scheduledAt): Valuation {
            $valuation->forceFill(['status' => ValuationStatus::Scheduled, 'scheduled_at' => $scheduledAt])->save();

            return $valuation->refresh();
        });
    }
}
