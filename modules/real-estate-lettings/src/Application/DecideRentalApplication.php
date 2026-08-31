<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Lettings\Application;

use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Lettings\Domain\RentalApplicationStatus;
use Liberu\RealEstate\Lettings\Models\RentalApplication;

final class DecideRentalApplication
{
    public function handle(RentalApplication $application, int|string $teamId, int|string $actorId, string $decision, ?string $notes = null): RentalApplication
    {
        abort_unless((string) $application->team_id === (string) $teamId, 404);
        if (! $application->isPending()) {
            throw ValidationException::withMessages(['decision' => 'Only pending applications can receive a final decision.']);
        }
        if (! in_array($decision, [RentalApplicationStatus::Approved->value, RentalApplicationStatus::Rejected->value], true)) {
            throw ValidationException::withMessages(['decision' => 'Choose approved or rejected.']);
        }
        if ($decision === RentalApplicationStatus::Approved && ! $application->isScreeningPassed()) {
            throw ValidationException::withMessages(['decision' => 'The application must pass screening before approval.']);
        }

        $application->forceFill(['status' => $decision, 'decided_at' => now(), 'decided_by' => $actorId, 'decision_notes' => $notes])->save();

        return $application->refresh();
    }
}
