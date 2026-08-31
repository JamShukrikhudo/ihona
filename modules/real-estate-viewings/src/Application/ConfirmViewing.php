<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Viewings\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Viewings\Domain\ViewingStatus;
use Liberu\RealEstate\Viewings\Models\Viewing;

final class ConfirmViewing
{
    public function handle(Viewing $viewing, int|string $teamId): Viewing
    {
        abort_unless((string) $viewing->team_id === (string) $teamId, 404);
        if (! $viewing->canTransitionTo(ViewingStatus::Confirmed)) {
            throw ValidationException::withMessages(['status' => 'Only requested viewings can be confirmed.']);
        }

        return DB::transaction(function () use ($viewing): Viewing {
            $viewing->forceFill(['status' => ViewingStatus::Confirmed])->save();

            return $viewing->refresh();
        });
    }
}
