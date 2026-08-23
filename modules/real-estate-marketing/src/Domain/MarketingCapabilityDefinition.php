<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Marketing\Domain;

final class MarketingCapabilityDefinition
{
    /** @return array<string, array{label: string, required: list<string>, behaviors: list<string>}> */
    public static function all(): array
    {
        $labels = ['Campaigns', 'Boards', 'Brochures', 'Portals', 'Website', 'Social/email', 'Attribution', 'Consent'];
        $result = [];
        foreach ($labels as $label) {
            $key = strtolower(str_replace([' ', '/', '-'], ['_', '_', '_'], $label));
            $result[$key] = ['label' => $label, 'required' => ['team_id', 'name', 'content'], 'behaviors' => ['lifecycle', 'validation', 'authorization', 'failure_recovery', 'audit', 'feedback']];
        }

        return $result;
    }
}
