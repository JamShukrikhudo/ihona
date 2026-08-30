<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Properties\Application;

use InvalidArgumentException;

final class EstimatePropertyTax
{
    public const BUYER_TYPES = ['first_time_buyer', 'home_mover', 'additional_property'];

    /** @param array<string, mixed> $options */
    /** @return array<string, mixed> */
    public function handle(float $purchasePrice, string $country = 'UK', array $options = []): array
    {
        if ($purchasePrice < 0) {
            throw new InvalidArgumentException('Purchase price cannot be negative.');
        }

        return match (strtoupper($country)) {
            'UK', 'GB', 'UNITED KINGDOM' => $this->uk($purchasePrice, $options),
            'US', 'USA', 'UNITED STATES' => $this->us($purchasePrice, $options),
            default => $this->generic($purchasePrice, $options),
        };
    }

    /** @param array<string, mixed> $options */
    private function uk(float $price, array $options): array
    {
        $buyerType = $this->buyerType($options['buyer_type'] ?? 'home_mover');
        $stampDuty = $this->bandedTax($price, $this->ukRates($buyerType));
        $legalFees = $this->tieredFee($price, [100000 => 850, 250000 => 1200, 500000 => 1500, 1000000 => 2000], 2500);
        $surveyFees = $this->tieredFee($price, [100000 => 400, 250000 => 600, 500000 => 900, 1000000 => 1200], 1500);
        $landRegistryFees = $this->tieredFee($price, [80000 => 40, 100000 => 80, 200000 => 190, 500000 => 270, 1000000 => 540], 910, true);
        $additional = $legalFees + $surveyFees + $landRegistryFees;

        return $this->result('United Kingdom', $price, $stampDuty, $additional, [
            'buyer_type' => $buyerType,
            'stamp_duty' => round($stampDuty, 2),
            'effective_tax_rate' => $this->rate($stampDuty, $price),
            'additional_costs' => ['legal_fees' => $legalFees, 'survey_fees' => $surveyFees, 'land_registry_fees' => $landRegistryFees],
        ]);
    }

    /** @param array<string, mixed> $options */
    private function us(float $price, array $options): array
    {
        $transferTax = $price * (float) ($options['transfer_tax_rate'] ?? 0.01);
        $annualTax = $price * (float) ($options['annual_tax_rate'] ?? 0.011);
        $recordingFees = 500.0;
        $titleInsurance = $price * 0.005;

        return $this->result('United States', $price, $transferTax, $recordingFees + $titleInsurance, [
            'transfer_tax' => round($transferTax, 2),
            'annual_property_tax' => round($annualTax, 2),
            'effective_tax_rate' => $this->rate($transferTax, $price),
            'additional_costs' => ['recording_fees' => $recordingFees, 'title_insurance' => round($titleInsurance, 2)],
        ]);
    }

    /** @param array<string, mixed> $options */
    private function generic(float $price, array $options): array
    {
        $tax = $price * (float) ($options['tax_rate'] ?? 0.03);
        $legalFees = $price * 0.01;

        return $this->result((string) ($options['country_name'] ?? 'Other'), $price, $tax, $legalFees + 1000, [
            'property_transfer_tax' => round($tax, 2),
            'effective_tax_rate' => $this->rate($tax, $price),
            'additional_costs' => ['legal_fees' => round($legalFees, 2), 'registration_fees' => 1000.0],
        ]);
    }

    /** @param array<string, mixed> $details */
    /** @return array<string, mixed> */
    private function result(string $country, float $price, float $tax, float $additional, array $details): array
    {
        $tax = round($tax, 2);
        $additional = round($additional, 2);

        return array_merge([
            'estimated' => true,
            'country' => $country,
            'purchase_price' => round($price, 2),
            'total_tax' => $tax,
            'total_additional_costs' => $additional,
            'total_cost' => round($price + $tax + $additional, 2),
        ], $details);
    }

    /** @param array<int|float, float> $rates */
    private function bandedTax(float $price, array $rates): float
    {
        $tax = 0.0;
        $previous = 0.0;
        foreach ($rates as $threshold => $rate) {
            if ($price > $threshold) {
                $tax += ((float) $threshold - $previous) * $rate;
                $previous = (float) $threshold;
                continue;
            }
            $tax += ($price - $previous) * $rate;
            break;
        }

        return $tax;
    }

    /** @param array<int, float|int> $fees */
    private function tieredFee(float $price, array $fees, float|int $last, bool $inclusive = false): float
    {
        foreach ($fees as $threshold => $fee) {
            if ($inclusive ? $price <= $threshold : $price < $threshold) {
                return (float) $fee;
            }
        }

        return (float) $last;
    }

    /** @return array<int|float, float> */
    private function ukRates(string $buyerType): array
    {
        return match ($buyerType) {
            'first_time_buyer' => [0 => 0, 300000 => 0, 500000 => 0.05, 925000 => 0.05, 1500000 => 0.10, PHP_INT_MAX => 0.12],
            'additional_property' => [0 => 0.03, 250000 => 0.03, 925000 => 0.08, 1500000 => 0.13, PHP_INT_MAX => 0.15],
            default => [0 => 0, 250000 => 0, 925000 => 0.05, 1500000 => 0.10, PHP_INT_MAX => 0.12],
        };
    }

    private function buyerType(mixed $buyerType): string
    {
        return in_array($buyerType, self::BUYER_TYPES, true) ? $buyerType : 'home_mover';
    }

    private function rate(float $tax, float $price): float
    {
        return $price > 0 ? round($tax / $price * 100, 2) : 0.0;
    }
}
