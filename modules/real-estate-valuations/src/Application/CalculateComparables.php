<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Valuations\Application;

use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Valuations\Models\Valuation;

final class CalculateComparables
{
    /** @param list<array<string, mixed>> $comparables */
    public function handle(Valuation $valuation, int|string $teamId, array $comparables): Valuation
    {
        abort_unless((string) $valuation->team_id === (string) $teamId, 404);
        if ($comparables === []) {
            throw ValidationException::withMessages(['comparable_data' => 'At least one comparable is required.']);
        }

        $amounts = array_values(array_filter(array_map(static fn (array $comparable): ?float => isset($comparable['amount']) && is_numeric($comparable['amount']) ? (float) $comparable['amount'] : null, $comparables), static fn (?float $amount): bool => $amount !== null && $amount >= 0));
        if ($amounts === []) {
            throw ValidationException::withMessages(['comparable_data' => 'Comparables must contain non-negative amounts.']);
        }

        $valuation->forceFill(['comparable_data' => ['items' => $comparables, 'count' => count($amounts), 'average_amount' => round(array_sum($amounts) / count($amounts), 2)]])->save();

        return $valuation->refresh();
    }
}
