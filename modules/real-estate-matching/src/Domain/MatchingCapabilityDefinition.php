<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Matching\Domain;

final class MatchingCapabilityDefinition
{
    /** @return array<string, array{label: string, required: list<string>, behaviors: list<string>}> */
    public static function all(): array
    {
        $labels = ['Applicant requirements', 'Affordability', 'Preferences', 'Scoring', 'Alerts', 'Feedback', 'Exclusions'];
        $result = [];
        foreach ($labels as $label) {
            $key = strtolower(str_replace(' ', '_', $label));
            $result[$key] = ['label' => $label, 'required' => ['team_id', 'applicant_id', 'criteria'], 'behaviors' => ['lifecycle', 'validation', 'authorization', 'failure_recovery', 'audit', 'feedback']];
        }

        return $result;
    }
}
