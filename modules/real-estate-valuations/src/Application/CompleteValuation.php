<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Valuations\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Valuations\Domain\ValuationStatus;
use Liberu\RealEstate\Valuations\Models\Valuation;

final class CompleteValuation
{
    /** @param array<string, mixed> $attributes */
    public function handle(Valuation $valuation, int|string $teamId, array $attributes): Valuation
    {
        abort_unless((string) $valuation->team_id === (string) $teamId, 404);
        if (! $valuation->canTransitionTo(ValuationStatus::Completed)) {
            throw ValidationException::withMessages(['status' => 'Only scheduled valuations can be completed.']);
        }
        if (! array_key_exists('valued_amount', $attributes) || (float) $attributes['valued_amount'] < 0) {
            throw ValidationException::withMessages(['valued_amount' => 'A non-negative valuation amount is required.']);
        }

        return DB::transaction(function () use ($valuation, $attributes): Valuation {
            $valuation->forceFill([
                'status' => ValuationStatus::Completed,
                'valued_amount' => $attributes['valued_amount'],
                'recommendation' => $attributes['recommendation'] ?? $valuation->recommendation,
                'completed_at' => now(),
                'follow_up_at' => $attributes['follow_up_at'] ?? $valuation->follow_up_at,
            ])->save();

            return $valuation->refresh();
        });
    }
}
