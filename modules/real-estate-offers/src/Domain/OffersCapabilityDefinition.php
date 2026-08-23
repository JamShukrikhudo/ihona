<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Offers\Domain;

final class OffersCapabilityDefinition
{
    /** @return array<string, array{label: string, required: list<string>, behaviors: list<string>}> */
    public static function all(): array
    {
        $labels = ['Terms', 'Qualification', 'Negotiation', 'Proof', 'Decision history', 'Accepted-offer controls'];
        $result = [];
        foreach ($labels as $label) {
            $key = strtolower(str_replace([' ', '-'], ['_', '_'], $label));
            $result[$key] = ['label' => $label, 'required' => ['team_id', 'property_id', 'amount'], 'behaviors' => ['lifecycle', 'validation', 'authorization', 'failure_recovery', 'audit', 'feedback']];
        }

        return $result;
    }
}
