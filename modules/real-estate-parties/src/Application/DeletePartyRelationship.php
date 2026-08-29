<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Parties\Application;

use Liberu\RealEstate\Parties\Models\PartyRelationship;

final class DeletePartyRelationship
{
    public function handle(PartyRelationship $relationship, int|string $teamId): void
    {
        abort_unless((string) $relationship->team_id === (string) $teamId, 404);
        $relationship->delete();
    }
}
