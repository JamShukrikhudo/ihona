<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Zoopla\Application;

use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Zoopla\Models\ZooplaSync;

final class UpdateZooplaSync
{
    public function handle(ZooplaSync $sync, int|string $teamId, array $attributes): ZooplaSync
    {
        if ((string) $sync->team_id !== (string) $teamId) {
            throw ValidationException::withMessages(['sync' => 'The Zoopla sync does not belong to this team.']);
        }$sync->fill($attributes)->save();

        return $sync->refresh();
    }
}
