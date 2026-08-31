<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertyManagement\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\PropertyManagement\Domain\VendorQuoteStatus;
use Liberu\RealEstate\PropertyManagement\Models\VendorQuote;

final class CreateVendorQuote
{
    public function handle(int|string $teamId, int|string $actorId, array $attributes): VendorQuote
    {
        foreach (['vendor_id', 'property_id', 'work_description', 'quote_amount', 'quote_date', 'valid_until'] as $field) {
            if (! filled($attributes[$field] ?? null)) {
                throw ValidationException::withMessages([$field => 'This field is required.']);
            }
        }
        if ((float) $attributes['quote_amount'] < 0 || now()->parse($attributes['valid_until'])->lt(now()->parse($attributes['quote_date']))) {
            throw ValidationException::withMessages(['valid_until' => 'The quote must remain valid on or after its quote date.']);
        }

        return DB::transaction(fn (): VendorQuote => VendorQuote::query()->create([...$attributes, 'team_id' => $teamId, 'requested_by' => $attributes['requested_by'] ?? $actorId, 'status' => VendorQuoteStatus::Pending]));
    }
}
