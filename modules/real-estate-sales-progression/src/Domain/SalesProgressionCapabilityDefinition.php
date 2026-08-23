<?php

declare(strict_types=1);

namespace Liberu\RealEstate\SalesProgression\Domain;

final class SalesProgressionCapabilityDefinition
{
    /** @return array<string, array{label: string, required: list<string>, behaviors: list<string>}> */
    public static function all(): array
    {
        $labels = ['Chain', 'Milestones', 'Memoranda', 'Legal/finance contacts', 'Dependencies', 'Risks', 'Completion'];
        $result = [];
        foreach ($labels as $label) {
            $key = strtolower(str_replace([' ', '/', '-'], ['_', '_', '_'], $label));
            $result[$key] = ['label' => $label, 'required' => ['team_id', 'property_id', 'status'], 'behaviors' => ['lifecycle', 'validation', 'authorization', 'failure_recovery', 'audit', 'feedback']];
        }

        return $result;
    }
}
