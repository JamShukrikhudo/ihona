<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Zoopla\Application;

use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Zoopla\Models\ZooplaSync;

final class DeleteZooplaSync
{
    public function handle(ZooplaSync $sync, int|string $teamId): void
    {
        if ((string) $sync->team_id !== (string) $teamId) {
            throw ValidationException::withMessages(['sync' => 'The Zoopla sync does not belong to this team.']);
        }$sync->delete();
    }
}
