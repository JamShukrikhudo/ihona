<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Viewings\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Viewings\Domain\ViewingStatus;
use Liberu\RealEstate\Viewings\Models\Viewing;

final class CancelViewing
{
    public function handle(Viewing $viewing, int|string $teamId, ?string $reason = null): Viewing
    {
        abort_unless((string) $viewing->team_id === (string) $teamId, 404);
        if (! $viewing->canTransitionTo(ViewingStatus::Cancelled)) {
            throw ValidationException::withMessages(['status' => 'This viewing cannot be cancelled.']);
        }

        return DB::transaction(function () use ($viewing, $reason): Viewing {
            $access = $viewing->access ?? [];
            if ($reason !== null) {
                $access['cancellation_reason'] = $reason;
            }
            $viewing->forceFill(['status' => ViewingStatus::Cancelled, 'access' => $access])->save();

            return $viewing->refresh();
        });
    }
}
