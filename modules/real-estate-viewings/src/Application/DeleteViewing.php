<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Viewings\Application;

use Liberu\RealEstate\Viewings\Models\Viewing;

final class DeleteViewing
{
    public function handle(Viewing $viewing, int|string $teamId): void
    {
        abort_unless((string) $viewing->team_id === (string) $teamId, 404);
        $viewing->delete();
    }
}
