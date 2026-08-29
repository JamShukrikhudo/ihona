<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Valuations\Domain;

final class ValuationsCapabilityDefinition
{
    /** @return array<string, array{label: string, required: list<string>, behaviors: list<string>}> */
    public static function all(): array
    {
        return self::define(['Appraisals', 'Comparables', 'Pricing', 'Fees', 'Recommendations', 'Follow-up', 'Conversion', 'Property valuation estimates']);
    }

    /** @param list<string> $labels */
    private static function define(array $labels): array
    {
        $result = [];
        foreach ($labels as $label) {
            $key = strtolower(str_replace([' ', '-'], ['_', '_'], $label));
            $result[$key] = ['label' => $label, 'required' => ['team_id', 'property_id', 'amount'], 'behaviors' => ['lifecycle', 'validation', 'authorization', 'failure_recovery', 'audit', 'feedback']];
        }

        return $result;
    }
}
