<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Viewings\Application;

use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Viewings\Models\Viewing;

final class UpdateViewing
{
    public function handle(Viewing $viewing, int|string $teamId, array $attributes): Viewing
    {
        abort_unless((string) $viewing->team_id === (string) $teamId, 404);
        if (array_key_exists('subject', $attributes) && trim((string) $attributes['subject']) === '') {
            throw ValidationException::withMessages(['subject' => 'A viewing subject is required.']);
        }$viewing->fill($attributes);
        $viewing->save();

        return $viewing->fresh();
    }
}
