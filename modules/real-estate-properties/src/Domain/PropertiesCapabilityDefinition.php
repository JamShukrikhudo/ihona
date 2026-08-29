<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Properties\Domain;

final class PropertiesCapabilityDefinition
{
    /** @return array<string, array{label: string, required: list<string>, behaviors: list<string>}> */
    public static function all(): array
    {
        $labels = ['Address/location', 'Categories', 'Templates', 'Favorites', 'Units', 'Characteristics', 'Tenure', 'Utilities', 'Features', 'Status', 'History', 'Keys'];
        $result = [];
        foreach ($labels as $label) {
            $key = strtolower(str_replace([' ', '/', '-'], ['_', '_', '_'], $label));
            $result[$key] = ['label' => $label, 'required' => ['team_id', 'address'], 'behaviors' => self::behaviors()];
        }

        return $result;
    }

    /** @return list<string> */
    private static function behaviors(): array
    {
        return ['lifecycle', 'validation', 'authorization', 'failure_recovery', 'audit', 'feedback'];
    }
}
