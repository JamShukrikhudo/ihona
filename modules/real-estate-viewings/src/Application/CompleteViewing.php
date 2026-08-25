<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Viewings\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Viewings\Domain\ViewingStatus;
use Liberu\RealEstate\Viewings\Models\Viewing;

final class CompleteViewing
{
    public function handle(Viewing $viewing, int|string $teamId, array $feedback = []): Viewing
    {
        abort_unless((string) $viewing->team_id === (string) $teamId, 404);
        if (! $viewing->canTransitionTo(ViewingStatus::Completed)) {
            throw ValidationException::withMessages(['status' => 'Only confirmed viewings can be completed.']);
        }

        return DB::transaction(function () use ($viewing, $feedback): Viewing {
            $viewing->forceFill(['status' => ViewingStatus::Completed, 'feedback' => $feedback])->save();

            return $viewing->refresh();
        });
    }
}
