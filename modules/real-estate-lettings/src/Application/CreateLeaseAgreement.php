<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Lettings\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Lettings\Domain\LeaseAgreementStatus;
use Liberu\RealEstate\Lettings\Models\LeaseAgreement;

final class CreateLeaseAgreement
{
    public function handle(int|string $teamId, array $attributes): LeaseAgreement
    {
        foreach (['property_id', 'start_date', 'end_date', 'monthly_rent'] as $field) {
            if (! filled($attributes[$field] ?? null)) {
                throw ValidationException::withMessages([$field => 'This field is required.']);
            }
        }
        if (now()->parse($attributes['end_date'])->lte(now()->parse($attributes['start_date']))) {
            throw ValidationException::withMessages(['end_date' => 'The end date must be after the start date.']);
        }
        $status = LeaseAgreementStatus::tryFrom((string) ($attributes['status'] ?? LeaseAgreementStatus::Draft->value));
        if ($status === null) {
            throw ValidationException::withMessages(['status' => 'Select a valid tenancy agreement status.']);
        }

        return DB::transaction(fn (): LeaseAgreement => LeaseAgreement::query()->create([...$attributes, 'team_id' => $teamId, 'status' => $status]));
    }
}
