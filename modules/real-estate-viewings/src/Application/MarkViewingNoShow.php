<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Viewings\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Viewings\Domain\ViewingStatus;
use Liberu\RealEstate\Viewings\Models\Viewing;

final class MarkViewingNoShow
{
    public function handle(Viewing $viewing, int|string $teamId, ?string $note = null): Viewing
    {
        abort_unless((string) $viewing->team_id === (string) $teamId, 404);
        if (! $viewing->canTransitionTo(ViewingStatus::NoShow)) {
            throw ValidationException::withMessages(['status' => 'Only confirmed viewings can be marked as a no-show.']);
        }

        return DB::transaction(function () use ($viewing, $note): Viewing {
            $feedback = $viewing->feedback ?? [];
            if ($note !== null) {
                $feedback['no_show_note'] = $note;
            }
            $viewing->forceFill(['status' => ViewingStatus::NoShow, 'no_show' => true, 'feedback' => $feedback])->save();

            return $viewing->refresh();
        });
    }
}
