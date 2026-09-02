<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertyManagement\Application;

use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\PropertyManagement\Domain\VendorQuoteStatus;
use Liberu\RealEstate\PropertyManagement\Models\VendorQuote;

final class DecideVendorQuote
{
    public function handle(VendorQuote $quote, int|string $teamId, int|string $actorId, string $decision, ?string $rejectionReason = null): VendorQuote
    {
        abort_unless((string) $quote->team_id === (string) $teamId, 404);
        if ($quote->status !== VendorQuoteStatus::Pending) {
            throw ValidationException::withMessages(['status' => 'Only pending quotes can be decided.']);
        }
        if (! in_array($decision, [VendorQuoteStatus::Accepted->value, VendorQuoteStatus::Rejected->value], true)) {
            throw ValidationException::withMessages(['decision' => 'Choose accepted or rejected.']);
        }
        if ($decision === VendorQuoteStatus::Rejected && blank($rejectionReason)) {
            throw ValidationException::withMessages(['rejection_reason' => 'A rejection reason is required.']);
        }
        $quote->forceFill(['status' => $decision, 'approved_by' => $decision === VendorQuoteStatus::Accepted->value ? $actorId : null, 'rejection_reason' => $rejectionReason])->save();

        return $quote->refresh();
    }
}
