<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Valuations\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Valuations\Domain\ValuationStatus;
use Liberu\RealEstate\Valuations\Models\Valuation;

final class ConvertValuation
{
    /** @param array<string, mixed> $conversion */
    public function handle(Valuation $valuation, int|string $teamId, array $conversion): Valuation
    {
        abort_unless((string) $valuation->team_id === (string) $teamId, 404);
        if (! $valuation->canTransitionTo(ValuationStatus::Converted)) {
            throw ValidationException::withMessages(['status' => 'Only completed valuations can be converted.']);
        }
        if (trim((string) ($conversion['type'] ?? '')) === '') {
            throw ValidationException::withMessages(['conversion.type' => 'A conversion type is required.']);
        }

        return DB::transaction(function () use ($valuation, $conversion): Valuation {
            $valuation->forceFill(['status' => ValuationStatus::Converted, 'conversion' => $conversion])->save();

            return $valuation->refresh();
        });
    }
}
