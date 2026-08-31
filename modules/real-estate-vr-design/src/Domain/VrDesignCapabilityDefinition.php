<?php

declare(strict_types=1);

namespace Liberu\RealEstate\VrDesign\Domain;

final class VrDesignCapabilityDefinition
{
    /** @return array<string, array{label: string, required: list<string>, behaviors: list<string>}> */
    public static function all(): array
    {
        $labels = ['Design styles', 'Furniture placement', 'Room layouts', 'Materials and lighting', 'Public templates', 'Thumbnail uploads', 'VR exports'];
        $result = [];
        foreach ($labels as $label) {
            $key = strtolower(str_replace([' ', '-'], ['_', '_'], $label));
            $result[$key] = ['label' => $label, 'required' => ['team_id', 'property_id', 'design_data'], 'behaviors' => ['lifecycle', 'validation', 'authorization', 'failure_recovery', 'audit', 'feedback']];
        }

        return $result;
    }
}
