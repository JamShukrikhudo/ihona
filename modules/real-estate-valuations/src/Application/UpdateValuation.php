<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Valuations\Application;

use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Valuations\Models\Valuation;

final class UpdateValuation
{
    public function handle(Valuation $valuation, int|string $teamId, array $attributes): Valuation
    {
        abort_unless((string) $valuation->team_id === (string) $teamId, 404);
        if (array_key_exists('subject', $attributes) && trim((string) $attributes['subject']) === '') {
            throw ValidationException::withMessages(['subject' => 'A valuation subject is required.']);
        } $valuation->fill($attributes);
        $valuation->save();

        return $valuation->fresh();
    }
}
