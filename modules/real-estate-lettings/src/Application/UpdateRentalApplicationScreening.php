<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Lettings\Application;

use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Lettings\Models\RentalApplication;

final class UpdateRentalApplicationScreening
{
    public function handle(RentalApplication $application, int|string $teamId, array $attributes): RentalApplication
    {
        abort_unless((string) $application->team_id === (string) $teamId, 404);
        if (! $application->isPending()) {
            throw ValidationException::withMessages(['status' => 'Screening cannot be changed after a final decision.']);
        }
        $application->forceFill($attributes)->save();

        return $application->refresh();
    }
}
