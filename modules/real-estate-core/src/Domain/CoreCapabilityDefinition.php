<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Core\Domain;

final class CoreCapabilityDefinition
{
    /** @return array<string, array{label: string, required: list<string>, behaviors: list<string>}> */
    public static function all(): array
    {
        return self::define(['Agencies', 'Branches', 'Teams', 'Territories', 'Terminology', 'Statuses', 'Numbering', 'Audit']);
    }

    /** @param list<string> $labels */
    private static function define(array $labels): array
    {
        $behaviors = ['lifecycle', 'validation', 'authorization', 'failure_recovery', 'audit', 'feedback'];
        $result = [];
        foreach ($labels as $label) {
            $key = strtolower(str_replace([' ', '/'], ['_', '_'], $label));
            $result[$key] = ['label' => $label, 'required' => ['team_id', 'name'], 'behaviors' => $behaviors];
        }

        return $result;
    }
}
