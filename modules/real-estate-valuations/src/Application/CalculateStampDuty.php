<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Valuations\Application;

use Illuminate\Validation\ValidationException;

final class CalculateStampDuty
{
    public function handle(float $purchasePrice, string $buyerType = 'home_mover'): array
    {
        if ($purchasePrice < 0 || ! in_array($buyerType, ['home_mover', 'first_time_buyer', 'additional_property'], true)) {
            throw ValidationException::withMessages(['purchase_price' => 'Purchase price and buyer type must be valid.']);
        }
        $threshold = $buyerType === 'first_time_buyer' ? 425000.0 : 250000.0;
        $tax = max(0.0, min($purchasePrice, 925000.0) - $threshold) * 0.05 + max(0.0, min($purchasePrice, 1500000.0) - 925000.0) * 0.1 + max(0.0, $purchasePrice - 1500000.0) * 0.12;

        return ['estimated' => true, 'purchase_price' => $purchasePrice, 'buyer_type' => $buyerType, 'stamp_duty' => round($tax, 2), 'disclaimer' => 'Estimate only; tax rules and reliefs vary.'];
    }
}
