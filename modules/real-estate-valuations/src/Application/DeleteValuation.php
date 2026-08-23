<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Valuations\Application;

use Liberu\RealEstate\Valuations\Models\Valuation;

final class DeleteValuation
{
    public function handle(Valuation $valuation, int|string $teamId): void
    {
        abort_unless((string) $valuation->team_id === (string) $teamId, 404);
        $valuation->delete();
    }
}
