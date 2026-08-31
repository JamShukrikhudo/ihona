<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Lettings\Application;

use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Lettings\Domain\LeaseAgreementStatus;
use Liberu\RealEstate\Lettings\Models\LeaseAgreement;

final class ServeLeaseNotice
{
    public function handle(LeaseAgreement $agreement, int|string $teamId, array $attributes): LeaseAgreement
    {
        abort_unless((string) $agreement->team_id === (string) $teamId, 404);
        if (! in_array($agreement->status, [LeaseAgreementStatus::Active, LeaseAgreementStatus::NoticeServed], true)) {
            throw ValidationException::withMessages(['status' => 'Only active agreements can receive notice.']);
        }
        if (now()->parse($attributes['notice_expires_at'] ?? '')->lt(now()->parse($attributes['notice_served_at'] ?? ''))) {
            throw ValidationException::withMessages(['notice_expires_at' => 'Notice expiry must be on or after service.']);
        }
        $agreement->forceFill([...$attributes, 'status' => LeaseAgreementStatus::NoticeServed])->save();

        return $agreement->refresh();
    }
}
