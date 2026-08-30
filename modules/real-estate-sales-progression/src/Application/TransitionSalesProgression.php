<?php

declare(strict_types=1);

namespace Liberu\RealEstate\SalesProgression\Application;

use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\SalesProgression\Domain\SalesProgressionStatus;
use Liberu\RealEstate\SalesProgression\Models\SalesProgression;

final class TransitionSalesProgression
{
    public function handle(SalesProgression $progression, int|string $teamId, SalesProgressionStatus $status): SalesProgression
    {
        if ((string) $progression->team_id !== (string) $teamId) {
            throw ValidationException::withMessages(['progression' => 'The progression does not belong to this team.']);
        }

        $current = $progression->status;
        $allowed = match ($current) {
            SalesProgressionStatus::InProgress => [SalesProgressionStatus::OnHold, SalesProgressionStatus::Exchanged, SalesProgressionStatus::FallenThrough],
            SalesProgressionStatus::OnHold => [SalesProgressionStatus::InProgress, SalesProgressionStatus::FallenThrough],
            SalesProgressionStatus::Exchanged => [SalesProgressionStatus::Completed, SalesProgressionStatus::FallenThrough],
            SalesProgressionStatus::Completed, SalesProgressionStatus::FallenThrough => [],
        };

        if (! in_array($status, $allowed, true)) {
            throw ValidationException::withMessages(['status' => "Cannot transition a {$current->value} progression to {$status->value}."]);
        }

        $attributes = ['status' => $status];
        if ($status === SalesProgressionStatus::Exchanged) {
            $attributes['exchanged_at'] = now();
        }
        if ($status === SalesProgressionStatus::Completed) {
            $attributes['completed_at'] = now();
        }
        $progression->forceFill($attributes)->save();

        return $progression->refresh();
    }
}
