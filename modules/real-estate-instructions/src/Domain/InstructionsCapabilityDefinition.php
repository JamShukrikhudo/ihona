<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Instructions\Domain;

final class InstructionsCapabilityDefinition
{
    /** @return array<string, array{label: string, required: list<string>, behaviors: list<string>}> */
    public static function all(): array
    {
        $labels = ['Agency agreements', 'Ownership checks', 'Terms', 'Disclosures', 'Approvals', 'Withdrawal'];
        $result = [];
        foreach ($labels as $label) {
            $key = strtolower(str_replace([' ', '-'], ['_', '_'], $label));
            $result[$key] = ['label' => $label, 'required' => ['team_id', 'property_id', 'party_id'], 'behaviors' => ['lifecycle', 'validation', 'authorization', 'failure_recovery', 'audit', 'feedback']];
        }

        return $result;
    }
}
