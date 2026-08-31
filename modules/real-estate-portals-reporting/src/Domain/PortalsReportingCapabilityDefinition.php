<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PortalsReporting\Domain;

final class PortalsReportingCapabilityDefinition
{
    /** @return array<string, array{label: string, required: list<string>, behaviors: list<string>}> */
    public static function all(): array
    {
        $labels = ['Party self-service plus pipeline', 'Conversion', 'Source', 'Fee', 'Time-to-complete', 'Occupancy', 'Compliance metrics'];
        $result = [];
        foreach ($labels as $label) {
            $key = strtolower(str_replace([' ', '-'], ['_', '_'], $label));
            $result[$key] = ['label' => $label, 'required' => ['team_id', 'metric', 'period'], 'behaviors' => ['lifecycle', 'validation', 'authorization', 'failure_recovery', 'audit', 'feedback']];
        }

        return $result;
    }
}
