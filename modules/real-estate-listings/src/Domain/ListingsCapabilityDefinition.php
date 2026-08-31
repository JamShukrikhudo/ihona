<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Listings\Domain;

final class ListingsCapabilityDefinition
{
    /** @return array<string, array{label: string, required: list<string>, behaviors: list<string>}> */
    public static function all(): array
    {
        $labels = ['Channel content', 'Pricing', 'Availability', 'Publication rules', 'Portal feeds', 'Reconciliation'];
        $result = [];
        foreach ($labels as $label) {
            $key = strtolower(str_replace([' ', '-'], ['_', '_'], $label));
            $result[$key] = ['label' => $label, 'required' => ['team_id', 'property_id', 'content'], 'behaviors' => ['lifecycle', 'validation', 'authorization', 'failure_recovery', 'audit', 'feedback']];
        }

        return $result;
    }
}
