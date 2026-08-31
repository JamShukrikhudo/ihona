<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Lettings\Application;

use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Lettings\Domain\RentalChargeStatus;
use Liberu\RealEstate\Lettings\Models\RentalCharge;

final class UpdateRentalCharge
{
    public function handle(RentalCharge $charge, int|string $teamId, array $attributes): RentalCharge
    {
        abort_unless((string) $charge->team_id === (string) $teamId, 404);
        if (array_key_exists('amount', $attributes) && (float) $attributes['amount'] < 0) {
            throw ValidationException::withMessages(['amount' => 'The charge amount cannot be negative.']);
        }
        if (array_key_exists('status', $attributes) && RentalChargeStatus::tryFrom((string) $attributes['status']) === null) {
            throw ValidationException::withMessages(['status' => 'Select a valid rental charge status.']);
        }
        $charge->forceFill($attributes)->save();

        return $charge->refresh();
    }
}
