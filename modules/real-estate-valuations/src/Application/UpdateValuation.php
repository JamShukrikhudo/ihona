<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Valuations\Application;

use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Valuations\Domain\ValuationStatus;
use Liberu\RealEstate\Valuations\Models\Valuation;

final class UpdateValuation
{
    public function handle(Valuation $valuation, int|string $teamId, array $attributes): Valuation
    {
        abort_unless((string) $valuation->team_id === (string) $teamId, 404);
        foreach (['comparable_data', 'recommendation'] as $field) {
            if (is_string($attributes[$field] ?? null)) {
                $attributes[$field] = json_decode($attributes[$field], true) ?: [];
            }
        }
        if (array_key_exists('subject', $attributes) && trim((string) $attributes['subject']) === '') {
            throw ValidationException::withMessages(['subject' => 'A valuation subject is required.']);
        }
        if (array_key_exists('status', $attributes)) {
            $status = ValuationStatus::tryFrom((string) $attributes['status']);
            if ($status === null || ! $valuation->canTransitionTo($status)) {
                throw ValidationException::withMessages(['status' => 'That valuation status transition is not allowed.']);
            }
        }
        $valuation->fill($attributes);
        $valuation->save();

        return $valuation->fresh();
    }
}
