<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Viewings\Domain;

final class ViewingsCapabilityDefinition
{
    /** @return array<string, array{label: string, required: list<string>, behaviors: list<string>}> */
    public static function all(): array
    {
        $labels = ['Availability', 'Booking', 'Confirmation', 'Access', 'Accompaniment', 'Reminders', 'Feedback', 'No-shows'];
        $result = [];
        foreach ($labels as $label) {
            $key = strtolower(str_replace([' ', '-'], ['_', '_'], $label));
            $result[$key] = ['label' => $label, 'required' => ['team_id', 'property_id', 'scheduled_at'], 'behaviors' => ['lifecycle', 'validation', 'authorization', 'failure_recovery', 'audit', 'feedback']];
        }

        return $result;
    }
}
