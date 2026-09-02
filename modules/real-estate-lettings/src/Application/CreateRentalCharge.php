<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Lettings\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Lettings\Domain\RentalChargeStatus;
use Liberu\RealEstate\Lettings\Models\RentalCharge;

final class CreateRentalCharge
{
    public function handle(int|string $teamId, int|string $actorId, array $attributes): RentalCharge
    {
        foreach (['property_id', 'amount', 'charge_date', 'description'] as $field) {
            if (! filled($attributes[$field] ?? null)) {
                throw ValidationException::withMessages([$field => 'This field is required.']);
            }
        }
        if ((float) $attributes['amount'] < 0) {
            throw ValidationException::withMessages(['amount' => 'The charge amount cannot be negative.']);
        }
        $status = RentalChargeStatus::tryFrom((string) ($attributes['status'] ?? RentalChargeStatus::Pending->value));
        if ($status === null) {
            throw ValidationException::withMessages(['status' => 'Select a valid rental charge status.']);
        }

        return DB::transaction(fn (): RentalCharge => RentalCharge::query()->create([...$attributes, 'team_id' => $teamId, 'created_by' => $attributes['created_by'] ?? $actorId, 'status' => $status]));
    }
}
