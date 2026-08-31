<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Lettings\Application;

use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Lettings\Domain\LeaseAgreementStatus;
use Liberu\RealEstate\Lettings\Models\LeaseAgreement;

final class RenewLeaseAgreement
{
    public function handle(LeaseAgreement $agreement, int|string $teamId, array $attributes): LeaseAgreement
    {
        abort_unless((string) $agreement->team_id === (string) $teamId, 404);
        if ($agreement->end_date === null || now()->parse($attributes['start_date'] ?? '')->lt($agreement->end_date)) {
            throw ValidationException::withMessages(['start_date' => 'A renewal must start on or after the current end date.']);
        }
        if (now()->parse($attributes['end_date'] ?? '')->lte(now()->parse($attributes['start_date']))) {
            throw ValidationException::withMessages(['end_date' => 'The renewal end date must be after its start date.']);
        }
        $renewal = $agreement->replicate(['status', 'is_signed', 'landlord_signed', 'tenant_signed', 'notice_type', 'notice_served_at', 'notice_expires_at', 'ended_at', 'end_reason']);
        $renewal->fill([...$attributes, 'team_id' => $teamId, 'renewal_of_id' => $agreement->getKey(), 'status' => LeaseAgreementStatus::Draft, 'is_signed' => false, 'landlord_signed' => false, 'tenant_signed' => false])->save();
        $agreement->forceFill(['status' => LeaseAgreementStatus::Renewed])->save();

        return $renewal->refresh();
    }
}
